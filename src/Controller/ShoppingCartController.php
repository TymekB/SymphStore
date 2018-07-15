<?php

namespace App\Controller;

use App\Entity\Product;
use App\ShoppingProcess\Cart;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ShoppingCartController extends Controller
{
    private $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function show()
    {
        $products = $this->cart->getProductList();
        $total = $this->cart->getTotalPrice();

        return $this->render('shopping_cart/show.html.twig', ['products' => $products, 'total' => $total]);
    }


    /**
     * @ParamConverter("product", class="App\Entity\Product")
     * @param Product $product
     * @return Response
     */
    public function addProduct(Product $product)
    {
        $productAdded = $this->cart->addProduct($product);

        if($productAdded) {
            $this->addFlash('success', 'Product has been added to your cart');
        } else {
            $this->addFlash('info', 'Product already exists in your cart');
        }

        return $this->redirectToRoute('product_show', ['name' => $product->getSlug()]);
    }

    /**
     * @ParamConverter("product", class="App\Entity\Product")
     * @param Product $product
     * @return Response
     * @throws \App\ShoppingProcess\CartException\ProductNotFoundException
     */
    public function deleteProduct(Product $product)
    {
        $productDeleted = $this->cart->deleteProduct($product);

        if($productDeleted) {
            $this->addFlash('success', 'Product deleted');
        }

        return $this->redirectToRoute('cart_show');
    }
}
