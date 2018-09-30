<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10.09.18
 * Time: 21:16
 */

namespace App\ShoppingProcess\Cart;


use App\Entity\Product;

class ProductReservationCounter
{
    /**
     * @var ProductReservator
     */
    private $productReservator;

    public function __construct(ProductReservator $productReservator)
    {
        $this->productReservator = $productReservator;
    }

    public function countByProductQuantity(Product $product)
    {
        $reservedProducts = $this->productReservator->getAllByProduct($product);

        $quantity = 0;

        foreach ($reservedProducts as $reservedProduct) {
            $quantity += $reservedProduct->getQuantity();
        }

        return $quantity;
    }

}