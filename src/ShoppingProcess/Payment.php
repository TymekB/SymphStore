<?php
/**
 * Created by PhpStorm.
 * User: tymek
 * Date: 13.07.18
 * Time: 11:26
 */

namespace App\ShoppingProcess;

use App\Entity\User;
use App\ShoppingProcess\PaymentException\TokenNotFound;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe;

class Payment
{
    /**
     * @var string
     */
    private $token;

    public function __construct($apiKey, $token = '')
    {
        Stripe::setApiKey($apiKey);

        $this->token = $token;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function process(User $user, Cart $cart, $currency = 'usd')
    {
        if(!$this->token) {
            throw new TokenNotFound();
        }

        $customer = Customer::create(['email' => $user->getEmail(), 'source' => $this->token]);

        $products = $cart->getProductList();
        $orderDetails = [];

        foreach($products as $product) {
            $charge = Charge::create(
                [
                    'amount' => $product->getPrice() * 100,
                    'currency' => $currency,
                    'description' => $product->getName(),
                    'customer' => $customer->id
                ]
            );

            $orderDetails[] = [
                'charge' => $charge,
                'product' => $product,
                'user' => $user
            ];
        }

        return $orderDetails;
    }

}