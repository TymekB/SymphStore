<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 02.09.18
 * Time: 18:12
 */

namespace App\ShoppingProcess\Cart\Decorators;


use App\Repository\ProductRepository;
use App\ShoppingProcess\Cart\Item;
use App\ShoppingProcess\Cart\ItemsCollection;
use App\ShoppingProcess\CartException\ProductsSizeIsNotEqualItemsSizeException;

class ItemsProductDecorator
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getItemsWithProducts(ItemsCollection $items): ItemsCollection
    {
        $products = $this->productRepository->findBy(
            [
                'id' => $items->getProductsId()
            ]);

        if(count($products) != count($items)) {
            throw new ProductsSizeIsNotEqualItemsSizeException();
        }

        $itemsWithProducts = $items->map(function(Item $item) use ($products) {

            foreach($products as $product) {

                if($product->getId() == $item->getProductId()) {
                    return $item->setProduct($product);
                }
            }

            return $item;

        });

        return $itemsWithProducts;
    }
}