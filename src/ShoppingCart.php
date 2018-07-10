<?php
/**
 * Created by PhpStorm.
 * User: tymek
 * Date: 07.07.18
 * Time: 19:52
 */

namespace App;


use App\Entity\Product;
use App\Serializer\ProductSerializer;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ShoppingCart
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var ProductSerializer
     */
    private $productSerializer;

    /**
     * ShoppingCart constructor.
     * @param SessionInterface $session
     * @param ProductSerializer $productSerializer
     */
    public function __construct(SessionInterface $session, ProductSerializer $productSerializer)
    {

        $this->session = $session;
        $this->productSerializer = $productSerializer;
        $this->session->start();
    }

    private function getBasket()
    {
        return $this->session->get('basket');
    }

    public function addProduct(Product $product)
    {
        $basket = $this->getBasket();

        if(!$basket) {
            $basket = [];
        }

        if($this->checkIfProductExists($product)) {
            return false;
        }

        $basket[] = $this->productSerializer->normalize($product);
        $this->session->set('basket', $basket);

        return true;
    }

    public function deleteProduct(Product $product)
    {
        $basket = $this->getBasket();

        if(!$basket) {
            return false;
        }

        foreach($basket as $key => $value) {
            if($product->getId() == $value['id']) {
                unset($basket[$key]);
                $this->session->set('basket', $basket);

                return true;
            }
        }

        return false;
    }

    public function getProductList()
    {
        $basket = $this->getBasket();

        if(!$basket) {
            return [];
        }

        return $basket;
    }

    public function getTotalPrice()
    {
        $basket = $this->getBasket();
        $total = 0;

        if(!$basket) {
            return $total;
        }

        foreach($basket as $value) {
            $total += $value['price'];
        }

        return $total;
    }

    public function checkIfProductExists(Product $product)
    {
        $basket = $this->getBasket();

        if(!$basket) {
            return false;
        }

        /** @var Product $value */
        foreach($basket as $value) {
            if($value['id'] == $product->getId()) {
                return true;
            }
        }

        return false;
    }

}