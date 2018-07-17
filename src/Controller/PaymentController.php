<?php

namespace App\Controller;

use App\Entity\Order;
use App\ShoppingProcess\Cart;
use App\ShoppingProcess\OrderCreator;
use App\ShoppingProcess\Payment;
use Doctrine\ORM\EntityManagerInterface;
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
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var OrderCreator
     */
    private $orderCreator;

    public function __construct(Cart $cart, Payment $payment, OrderCreator $orderCreator, EntityManagerInterface $em)
    {
        $this->cart = $cart;
        $this->payment = $payment;
        $this->em = $em;
        $this->orderCreator = $orderCreator;
    }

    public function processPayment(Request $request)
    {
        $total = $this->cart->getTotalPrice();
        $token = $request->request->get('stripeToken');

        if($token && $total > 0) {
            $this->payment->setToken($token);
            $orderDetails = $this->payment->process($this->getUser(), $this->cart);

            $this->orderCreator->create($orderDetails);

            return new Response('Your payment has been processed');
        }

        return $this->render('payment/process_payment.html.twig', ['total' => $total]);
    }
}
