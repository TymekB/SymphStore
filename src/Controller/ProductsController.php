<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductsController extends Controller
{
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * ProductsController constructor.
     * @param ProductRepository $productRepository
     */
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
     */
    public function show(Product $product)
    {
        return $this->render('products/show.html.twig', ['product' => $product]);
    }
}
