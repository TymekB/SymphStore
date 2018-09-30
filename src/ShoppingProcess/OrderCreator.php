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
    /**
     * @var ProductQuantitySubtractor
     */
    private $productQuantitySubtractor;

    public function __construct(EntityManagerInterface $em, ProductQuantitySubtractor $productQuantitySubtractor)
    {
        $this->em = $em;
        $this->productQuantitySubtractor = $productQuantitySubtractor;
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

        $this->productQuantitySubtractor->subtract($orderDetails);

        return true;
    }
}