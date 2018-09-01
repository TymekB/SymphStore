<?php
/**
 * Created by PhpStorm.
 * User: tymek
 * Date: 17.07.18
 * Time: 15:37
 */

namespace App\ShoppingProcess;


use App\Entity\Order;
use App\ShoppingProcess\Order\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;

class OrderCreator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function create(OrderDetails $orderDetails)
    {
        $order = new Order();
        $order->setUser($orderDetails->getUser());

        foreach($orderDetails->getOrderedProducts() as $product) {
            $order->addOrderedProduct($product);

            $this->em->persist($product);
        }

        $this->em->persist($order);
        $this->em->flush();

        return true;
    }
}