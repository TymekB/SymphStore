<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 31.08.18
 * Time: 15:04
 */

namespace App\ShoppingProcess\Order;


use App\Entity\OrderedProduct;
use App\Entity\User;

class OrderDetails
{
    private $user;
    private $orderedProducts = [];

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getOrderedProducts()
    {
        return $this->orderedProducts;
    }

    public function addOrderedProduct(OrderedProduct $product)
    {
        $this->orderedProducts[] = $product;

        return $this;
    }


}