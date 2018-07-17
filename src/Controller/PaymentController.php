<?php

namespace App\Controller;

use App\ShoppingProcess\Cart;
use App\ShoppingProcess\Payment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    private $cart;
    /**
     * @var Payment
     */
    private $payment;

    public function __construct(Cart $cart, Payment $payment)
    {
        $this->cart = $cart;
        $this->payment = $payment;
    }

    public function processPayment(Request $request)
    {
        $total = $this->cart->getTotalPrice();
        $token = $request->request->get('stripeToken');

        if($token) {
            $this->payment->setToken($token);

            $orderDetails = $this->payment->process($this->getUser(), $this->cart);

            return new Response('Your payment has been processed');
        }

        return $this->render('payment/process_payment.html.twig', ['total' => $total]);
    }
}
