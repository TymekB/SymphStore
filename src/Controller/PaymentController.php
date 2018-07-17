<?php

namespace App\Controller;

use App\Entity\Order;
use App\ShoppingProcess\Cart;
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

    public function __construct(Cart $cart, Payment $payment, EntityManagerInterface $em)
    {
        $this->cart = $cart;
        $this->payment = $payment;
        $this->em = $em;
    }

    public function processPayment(Request $request)
    {
        $total = $this->cart->getTotalPrice();
        $token = $request->request->get('stripeToken');

        if($token) {
            $this->payment->setToken($token);

            $orderDetails = $this->payment->process($this->getUser(), $this->cart);

            foreach($orderDetails as $value) {
                $order = new Order();

                $order->setId($value['charge']->id);
                $order->setUser($value['user']);
                $order->setProduct($value['product']);

                $this->em->persist($order);
            }

            $this->em->flush();

            return new Response('Your payment has been processed');
        }

        return $this->render('payment/process_payment.html.twig', ['total' => $total]);
    }
}
