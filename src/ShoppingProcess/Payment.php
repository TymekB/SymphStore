<?php
/**
 * Created by PhpStorm.
 * User: tymek
 * Date: 13.07.18
 * Time: 11:26
 */

namespace App\ShoppingProcess;

use App\Entity\OrderedProduct;
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

        $productList = $cart->getProductList();
        $orderDetails = ['user' => $user, 'products' => []];

        foreach($productList as $value) {
            Charge::create(
                [
                    'amount' => ($value['product']->getPrice() * 100) * $value['quantity'],
                    'currency' => $currency,
                    'description' => $value['product']->getName().' ('.$value['quantity'].')',
                    'customer' => $customer->id
                ]
            );

            $orderedProduct = new OrderedProduct();

            $orderedProduct->setProduct($value['product']);
            $orderedProduct->setQuantity($value['quantity']);

            $orderDetails['products'][] = $orderedProduct;
        }

        return $orderDetails;
    }

}