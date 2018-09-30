<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 21.08.18
 * Time: 15:36
 */

namespace App\ShoppingProcess\Cart;

use App\Entity\Product;

class Item
{
    /**
     * @var Product
     */
    private $product;

    /**
     * @var Int
     */
    private $quantity;

    /**
     * @var Int
     */
    private $productId;

    public function setProductId(Int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }
    
    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Int
     */
    public function getQuantity(): Int
    {
        return $this->quantity;
    }

    /**
     * @param Int $quantity
     * @return Item
     */
    public function setQuantity(Int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @param Int $quantity
     */
    public function addQuantity(Int $quantity): void
    {
        $this->quantity += $quantity;
    }
}
