<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 29.09.18
 * Time: 14:00
 */

namespace App\ShoppingProcess;


use App\ShoppingProcess\Order\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;

class ProductQuantitySubtractor
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function subtract(OrderDetails $orderDetails)
    {
        foreach($orderDetails->getOrderedProducts() as $orderedProduct) {
            $product = $orderedProduct->getProduct();
            $productQuantity = $orderedProduct->getProduct()->getQuantity();

            $quantity = $productQuantity - $orderedProduct->getQuantity();

            $product->setQuantity($quantity);

            $this->em->persist($product);

        }
        $this->em->flush();

        return true;
    }

}