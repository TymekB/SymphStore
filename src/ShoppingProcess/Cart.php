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
use App\ShoppingProcess\CartException\ProductsFieldsNotFoundInProductList;
use App\ShoppingProcess\CartException\ProductsSizeIsNotEqualBasketSize;
use App\ShoppingProcess\CartException\ProductsSizeIsNotEqualProductListSize;
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

    private function getBasket()
    {
        return $this->session->get('basket', []);
    }

    public function getProductList()
    {
        $basket = $this->getBasket();
        $productsId = array_column($basket, 'id');

        $products = $this->productRepository->findBy(['id' => $productsId]);

        if(count($products) != count($basket)) {
            throw new ProductsSizeIsNotEqualBasketSize();
        }

        $productList = [];

        foreach($products as $product) {
            foreach($basket as $value) {
                if($product->getId() == $value['id']) {
                    $productList[] =  [
                        'product' => $product,
                        'quantity' => $value['quantity']
                    ];
                }
            }
        }

        return $productList;
    }

    public function getTotalPrice()
    {
        $productList = $this->getProductList();
        $total = 0;

        if(!$productList) {
            return $total;
        }

        foreach($productList as $value) {
            $total += $value['product']->getPrice() * $value['quantity'];
        }

        return $total;
    }

    public function addProduct(Product $product, int $quantity = 1)
    {
        $basket = $this->getBasket();

        $productKey = $this->getProductKey($product);

        if($productKey === false) {
            $basket[] = ['id' => $product->getId(), 'quantity' => $quantity];
        }
        else {
            $basket[$productKey]['quantity'] += 1;
        }

        $this->setBasket($basket);

        return true;
    }

    public function updateProducts(array $products)
    {
        $basket = $this->getBasket();
        $productList = $this->getProductList();

        if(count($products) != count($productList)) {
            throw new ProductsSizeIsNotEqualProductListSize();
        }

        foreach($productList as $key => $value) {
            foreach($products as $product) {
                if($product['id'] == $value['product']->getId() && $product['price'] == $value['product']->getPrice()) {

                    if ($product['quantity'] > 0) {

                        $key = $this->getProductKey($value['product']);

                        if ($key) {
                            $basket[$key]['quantity'] = $product['quantity'];
                        }
                    }
                }
            }
        }

        $this->setBasket($basket);

        return true;

    }

    private function getProductKey(Product $product)
    {
        $basket = $this->getBasket();
        $productFound = false;

        foreach($basket as $key => $value) {
            if($product->getId() == $value['id']) {
                $productFound = $key;
            }
        }

        return $productFound;
    }

    public function deleteProduct(Product $product)
    {
        $basket = $this->getBasket();
        $productFound = $this->getProductKey($product);

        if($productFound === false) {
            throw new ProductNotFoundException();
        }

        unset($basket[$productFound]);
        $this->setBasket($basket);

        return true;
    }
}