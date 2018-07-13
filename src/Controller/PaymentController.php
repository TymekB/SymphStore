<?php

namespace App\Controller;

use App\ShoppingProcess\Cart;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PaymentController extends Controller
{
    private $shoppingCart;

    public function __construct(Cart $cart)
    {
        $this->shoppingCart = $cart;
    }

    public function processPayment(Request $request)
    {
        $total = $this->shoppingCart->getTotalPrice();
        $token = $request->request->get('stripeToken');

        if($token) {

            Stripe::setApiKey(getenv('STRIPE_SECRET'));

            $customer = Customer::create(
                [
                    'email' => $this->getUser()->getEmail(),
                    'source' => $token
                ]
            );

            $charge = Charge::create(
                [
                    'amount' => $total * 100,
                    'currency' => 'usd',
                    'description' => 'test',
                    'customer' => $customer->id
                ]
            );
        }

        return $this->render('payment/process_payment.html.twig', ['total' => $total]);
    }
}
