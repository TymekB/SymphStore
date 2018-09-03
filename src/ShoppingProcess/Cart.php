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
use App\ShoppingProcess\Cart\Decorators\ItemsProductDecorator;
use App\ShoppingProcess\Cart\ItemsCollection;
use App\ShoppingProcess\Cart\Item;
use App\ShoppingProcess\CartException\ItemNotFoundException;
use App\ShoppingProcess\CartException\ProductNotInStockException;
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
     * @var ItemsProductDecorator
     */
    private $itemsProductDecorator;

    public function __construct(SessionInterface $session, ItemsProductDecorator $itemsProductDecorator)
    {
        $this->session = $session;
        $this->session->start();

        $this->itemsProductDecorator = $itemsProductDecorator;
    }

    public function getItems(): ItemsCollection
    {
        return $this->session->get('items', new ItemsCollection());
    }

    private function setItems(ItemsCollection $items)
    {
        $this->session->set('items', $items);

        return $this;
    }

    public function removeItems()
    {
        $this->session->set("items", new ItemsCollection());

        return true;
    }

    public function getTotalAmount()
    {
        $items = $this->itemsProductDecorator->getItemsWithProducts($this->getItems());
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
        if($product->getQuantity() < $quantity) {
            throw new ProductNotInStockException();
        }

        $items = $this->getItems();
        $item = $items->searchItemByProductId($product->getId());

        if(!$item) {
            $item = new Item();
            $item->setProductId($product->getId())->setQuantity($quantity);
        }
        else {

            if($product->getQuantity() + $quantity < $item->getQuantity()) {
                throw new ProductNotInStockException();
            }

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
        $itemsWithProducts = $this->itemsProductDecorator->getItemsWithProducts($this->getItems());

        if (count($products) != count($itemsWithProducts)) {
            throw new ProductsSizeIsNotEqualItemsSizeException();
        }

        $itemsWithProducts->map(function(Item $item) use($products) {

                foreach($products as $product) {

                    if ($product->id == $item->getProduct()->getId() && $product->price == $item->getProduct()->getPrice()) {

                        if($item->getProduct()->getQuantity() < $product->quantity || $product->quantity <= 0) {

                            throw new ProductNotInStockException($item->getProduct()->getName(). " not in stock.");
                        }

                        return $item->setQuantity($product->quantity);
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