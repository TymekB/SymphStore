<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OrdersController extends Controller
{
    public function list()
    {
        $orders = $this->getUser()->getOrders();

        return $this->render('orders/list.html.twig', ['orders' => $orders]);
    }
}
