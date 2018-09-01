<?php
/**
 * Created by PhpStorm.
 * User: tymek
 * Date: 07.07.18
 * Time: 19:52
 */

namespace App\ShoppingProcess;


use App\Entity\Product;
use App\Repository\ProductRepository;
use App\ShoppingProcess\Cart\ItemsCollection;
use App\ShoppingProcess\Cart\Item;
use App\ShoppingProcess\CartException\ItemNotFoundException;
use App\ShoppingProcess\CartException\ProductsSizeIsNotEqualItemsSizeException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->session->start();

        $this->productRepository = $productRepository;
    }

    private function getItems(): ItemsCollection
    {
        return $this->session->get('items', new ItemsCollection());
    }

    public function setItems(ItemsCollection $items)
    {
        $this->session->set('items', $items);

        return $this;
    }

    public function getItemsWithProducts(): ItemsCollection
    {
        $items = $this->getItems();

        $products = $this->productRepository->findBy(
            [
                'id' => $items->getProductsId()
            ]);

        if(count($products) != count($items)) {
            throw new ProductsSizeIsNotEqualItemsSizeException();
        }

        $itemsWithProducts = $items->map(function(Item $item) use ($products){

            foreach($products as $product) {

                if($product->getId() == $item->getProductId()) {
                    return $item->setProduct($product);
                }
            }

            return $item;

        });

        return $itemsWithProducts;
    }

    public function getTotalAmount()
    {
        $items = $this->getItemsWithProducts();
        $total = 0;

        if(!$items) {
            return $total;
        }

        $total = $items->map(function(Item $item){
            return $item->getProduct()->getPrice() * $item->getQuantity();
        });

        return array_sum($total->toArray());
    }

    public function addProduct(Product $product, int $quantity = 1)
    {
        $items = $this->getItems();
        $item = $items->searchItemByProductId($product->getId());

        if(!$item) {
            $item = new Item();
            $item->setProductId($product->getId())->setQuantity($quantity);
        }
        else {
            $item->addQuantity($quantity);
        }

        $items->addItem($item);

        $this->setItems($items);

        return true;
    }

    /**
     * @param array $products
     * @return bool
     * @throws ProductsSizeIsNotEqualItemsSizeException
     */
    public function updateProducts(array $products): bool
    {
        $itemsWithProducts = $this->getItemsWithProducts();

        if (count($products) != count($itemsWithProducts)) {
            throw new ProductsSizeIsNotEqualItemsSizeException();
        }

        $itemsWithProducts->map(function(Item $item) use($products) {

                foreach($products as $product) {

                    if ($product->id == $item->getProduct()->getId() && $product->price == $item->getProduct()->getPrice()) {

                        if($product->quantity > 0) {
                            return $item->setQuantity($product->quantity);
                        }

                    }
                }

                return $item;
        });

        return true;
    }

    public function deleteProduct(Product $product)
    {
        $items = $this->getItems();
        $item = $items->searchItemByProductId($product->getID());

        if(!$item) {
            throw new ItemNotFoundException();
        }

        $items->deleteItem($item);

        return true;
    }
}