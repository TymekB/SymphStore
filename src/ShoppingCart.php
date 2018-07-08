<?php
/**
 * Created by PhpStorm.
 * User: tymek
 * Date: 07.07.18
 * Time: 19:52
 */

namespace App;


use App\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ShoppingCart
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * ShoppingCart constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {

        $this->session = $session;
        $this->session->start();
    }

    public function addProduct(Product $product)
    {
        $basket = $this->session->get('basket');

        if(!$basket) {
            $this->session->set('basket', [$product]);
        }

        if(!$this->checkIfProductExist($product)) {
            $basket[] = $product;
            $this->session->set('basket', $basket);

            return true;
        }

        return false;
    }

    public function deleteProduct(Product $product)
    {
        $basket = $this->session->get('basket');

        if(!$basket) {
            return false;
        }

        foreach($basket as $key => $value) {
            if($product->getId() == $value->getId()) {
                unset($basket[$key]);
                $this->session->set('basket', $basket);
                return true;
            }
        }

        return false;
    }

    public function getProductList()
    {
        $basket = $this->session->get('basket');

        if(!$basket) {
            return [];
        }

        return $basket;
    }

    public function checkIfProductExist(Product $product)
    {
        $basket = $this->session->get('basket');

        /** @var Product $value */
        foreach($basket as $value) {
            if($value->getId() == $product->getId()) {
                return true;
            }
        }

        return false;
    }

}