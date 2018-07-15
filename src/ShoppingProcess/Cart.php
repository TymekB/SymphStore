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
use App\ShoppingProcess\CartException\ProductNotFoundException;
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

    private function setBasket(array $basket)
    {
        $this->session->set('basket', $basket);
    }

    public function getBasket()
    {
        return $this->session->get('basket', []);
    }

    public function addProduct(Product $product)
    {
        $basket = $this->getBasket();

        $basket[] = $product->getId();
        $this->setBasket($basket);

        return true;
    }

    public function deleteProduct(Product $product)
    {
        $basket = $this->getBasket();

        $productFound = array_search($product->getId(), $basket);

        if($productFound === false) {
            throw new ProductNotFoundException();
        }

        unset($basket[$productFound]);
        $this->setBasket($basket);

        return true;
    }

    public function getProductList()
    {
        $basket = $this->getBasket();

        if(!$basket) {
            return false;
        }

        return $this->productRepository->findBy(['id' => $basket]);
    }

    public function getTotalPrice()
    {
        $products = $this->getProductList();
        $total = 0;

        if(!$products) {
            return $total;
        }

        foreach($products as $product) {
            $total += $product->getPrice();
        }

        return $total;
    }

    public function checkIfProductExists(Product $product)
    {
        $basket = $this->getBasket();

        return isset($basket[$product->getId()]);
    }
}