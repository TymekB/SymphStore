<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\ShoppingCart;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProductsController extends Controller
{
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var ShoppingCart
     */
    private $shoppingCart;

    /**
     * ProductsController constructor.
     * @param ProductRepository $productRepository
     * @param SessionInterface $session
     * @param ShoppingCart $shoppingCart
     */
    public function __construct(ProductRepository $productRepository, SessionInterface $session, ShoppingCart $shoppingCart)
    {
        $this->productRepository = $productRepository;
        $this->session = $session;
        $this->shoppingCart = $shoppingCart;
    }


    public function index()
    {
        $products = $this->productRepository->findAll();

        return $this->render('products/index.html.twig', ['products' => $products]);
    }

    /**
     * @ParamConverter("product", options={"mapping": {"name" = "slug"}})
     */
    public function show(Product $product)
    {
        return $this->render('products/show.html.twig', ['product' => $product]);
    }

    /**
     * @ParamConverter("product", class="App\Entity\Product")
     * @param Product $product
     * @return Response
     */
    public function addToShoppingCart(Product $product)
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
    public function deleteFromShoppingCart(Product $product)
    {
        $productDeleted = $this->shoppingCart->deleteProduct($product);

        if($productDeleted) {
            $this->addFlash('success', 'Product deleted');
        }

        return $this->redirectToRoute('cart_show');
    }

    public function showShoppingCart()
    {
        $products = $this->shoppingCart->getProductList();

        return $this->render('products/shoppingCart.html.twig', ['products' => $products]);
    }
}
