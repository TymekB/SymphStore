<?php

namespace App\Controller;

use App\Entity\Order;
use App\ShoppingProcess\Cart;
use App\ShoppingProcess\Cart\Decorators\ItemsProductDecorator;
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
    /**
     * @var ItemsProductDecorator
     */
    private $itemsProductDecorator;

    public function __construct(Cart $cart, Payment $payment, OrderCreator $orderCreator, EntityManagerInterface $em, ItemsProductDecorator $itemsProductDecorator)
    {
        $this->cart = $cart;
        $this->payment = $payment;
        $this->em = $em;
        $this->orderCreator = $orderCreator;
        $this->itemsProductDecorator = $itemsProductDecorator;
    }

    public function processPayment(Request $request)
    {
        $total = $this->cart->getTotalAmount();
        $token = $request->request->get('stripeToken');

        if($total <= 0) {
            return $this->redirectToRoute("index");
        }

        if($token) {

            $items = $this->itemsProductDecorator->getItemsWithProducts($this->cart->getItems());

            $this->payment->setToken($token);
            $this->payment->process($this->getUser(), $items);

            $orderDetails = $this->payment->getOrderDetails();
            $this->orderCreator->create($orderDetails);

            $this->cart->removeItems();

            $this->addFlash('success', "Thank you for purchasing!");

            return $this->redirectToRoute("order_details");
        }

        return $this->render('payment/process_payment.html.twig', ['total' => $total]);
    }
}
