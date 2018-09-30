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
use App\ShoppingProcess\Order\OrderDetails;
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
    /**
     * @var OrderDetails
     */
    private $orderDetails;

    public function __construct(OrderDetails $orderDetails, $apiKey, $token = '')
    {
        Stripe::setApiKey($apiKey);

        $this->token = $token;
        $this->orderDetails = $orderDetails;
    }

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function getOrderDetails()
    {
        return $this->orderDetails;
    }

    public function process(User $user, $items, $currency = 'usd')
    {
        if(!$this->token) {
            throw new TokenNotFound();
        }

        $this->orderDetails->setUser($user);
        $customer = Customer::create(['email' => $user->getEmail(), 'source' => $this->token]);

        foreach($items as $item) {
            Charge::create(
                [
                    'amount' => ($item->getProduct()->getPrice() * 100) * $item->getQuantity(),
                    'currency' => $currency,
                    'description' => $item->getProduct()->getName().' ('.$item->getQuantity().')',
                    'customer' => $customer->id
                ]
            );

            $orderedProduct = new OrderedProduct();

            $orderedProduct->setProduct($item->getProduct());
            $orderedProduct->setQuantity($item->getQuantity());

            $this->orderDetails->addOrderedProduct($orderedProduct);
        }

        return true;
    }

}