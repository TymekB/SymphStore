<?php

namespace App\Controller;

use App\Entity\Product;
use App\ShoppingProcess\Cart;
use App\ShoppingProcess\Cart\Decorators\ItemsProductDecorator;
use App\ShoppingProcess\Cart\ProductReservator;
use App\ShoppingProcess\CartException\ProductNotInStockException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ShoppingCartController extends Controller
{
    private $cart;
    /**
     * @var ItemsProductDecorator
     */
    private $itemsProductDecorator;
    /**
     * @var ProductReservator
     */
    private $productReservator;

    public function __construct(Cart $cart, ItemsProductDecorator $itemsProductDecorator, ProductReservator $productReservator)
    {
        $this->cart = $cart;
        $this->itemsProductDecorator = $itemsProductDecorator;
        $this->productReservator = $productReservator;
    }

    public function show()
    {
        $items = $this->itemsProductDecorator->getItemsWithProducts($this->cart->getItems());
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
        try {
            $productAdded = $this->cart->addProduct($product);

            if($productAdded) {

                $this->productReservator->removeAll();
                $this->productReservator->create($product);

                $this->addFlash('success', 'Product has been added to your cart');
            }
            else {
                $this->addFlash('info', 'Product already exists in your cart');
            }
        }
        catch(ProductNotInStockException $e) {
            $this->addFlash("info", "Product not in stock");
        }

        return $this->redirectToRoute('product_show', ['name' => $product->getSlug()]);
    }

    public function updateProducts(Request $request)
    {
        $products = $request->request->get('products');
        $result = ["success" => true, "msg" => "Products updated."];

        if(!$request->isXmlHttpRequest() || !$products) {
            throw $this->createNotFoundException();
        }

        $jsonDecode = new JsonDecode();
        $products = $jsonDecode->decode($products, JsonEncoder::FORMAT);

        try {
            $this->cart->updateProducts($products);
        }
        catch(ProductNotInStockException $e) {
            $result['success'] = false;
            $result['msg'] = $e->getMessage();
        }

        return $this->json($result);
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
