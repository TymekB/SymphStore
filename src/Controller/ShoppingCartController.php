<?php

namespace App\Controller;

use App\Entity\Product;
use App\ShoppingCart;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ShoppingCartController extends Controller
{
    /**
     * @var ShoppingCart
     */
    private $shoppingCart;

    public function __construct(ShoppingCart $shoppingCart)
    {
        $this->shoppingCart = $shoppingCart;
    }

    public function show()
    {
        $products = $this->shoppingCart->getProductList();
        $total = $this->shoppingCart->getTotalPrice();

        return $this->render('products/shoppingCart.html.twig', ['products' => $products, 'total' => $total]);
    }


    /**
     * @ParamConverter("product", class="App\Entity\Product")
     * @param Product $product
     * @return Response
     */
    public function addProduct(Product $product)
    {
        $productAdded = $this->shoppingCart->addProduct($product);

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
     */
    public function deleteProduct(Product $product)
    {
        $productDeleted = $this->shoppingCart->deleteProduct($product);

        if($productDeleted) {
            $this->addFlash('success', 'Product deleted');
        }

        return $this->redirectToRoute('cart_show');
    }
}
