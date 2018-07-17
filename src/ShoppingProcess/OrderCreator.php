<?php
/**
 * Created by PhpStorm.
 * User: tymek
 * Date: 17.07.18
 * Time: 15:37
 */

namespace App\ShoppingProcess;


use App\Entity\Order;
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

    public function create(array $orderDetails)
    {
        foreach($orderDetails as $value) {
            $order = new Order();
            $order->setId($value['charge']->id);
            $order->setProduct($value['product']);
            $order->setUser($value['user']);

            $this->em->persist($order);
        }

        $this->em->flush();

        return true;
    }
}