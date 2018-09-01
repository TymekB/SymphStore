<?php

namespace App\Controller;

use App\Entity\Product;
use App\ShoppingProcess\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ShoppingCartController extends Controller
{
    private $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function show()
    {
        $items = $this->cart->getItemsWithProducts();
        $total = $this->cart->getTotalAmount();

        return $this->render('shopping_cart/show.html.twig', ['items' => $items, 'total' => $total]);
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

    public function updateProducts(Request $request)
    {
        $products = $request->request->get('products');

        if(!$request->isXmlHttpRequest() || !$products) {
            throw $this->createNotFoundException();
        }

        $jsonDecode = new JsonDecode();
        $products = $jsonDecode->decode($products, JsonEncoder::FORMAT);

        $success = $this->cart->updateProducts($products);

        return $this->json(['success' => $success]);
    }

    /**
     * @ParamConverter("product", class="App\Entity\Product")
     * @param Product $product
     * @return Response
     * @throws \App\ShoppingProcess\CartException\ItemNotFoundException
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
