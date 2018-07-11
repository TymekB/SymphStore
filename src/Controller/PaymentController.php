<?php

namespace App\Controller;

use App\ShoppingCart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PaymentController extends Controller
{
    /**
     * @var ShoppingCart
     */
    private $shoppingCart;

    public function __construct(ShoppingCart $shoppingCart)
    {
        $this->shoppingCart = $shoppingCart;
    }

    public function processPayment(Request $request)
    {
        $total = $this->shoppingCart->getTotalPrice();

        return $this->render('payment/process_payment.html.twig', ['total' => $total]);
    }

}
