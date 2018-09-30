<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProductsController extends Controller
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }


    public function index()
    {
        $products = $this->productRepository->findAll();

        return $this->render('products/index.html.twig', ['products' => $products]);
    }

    /**
     * @ParamConverter("product", options={"mapping": {"name" = "slug"}})
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Product $product)
    {
        return $this->render('products/show.html.twig', ['product' => $product]);
    }
}
