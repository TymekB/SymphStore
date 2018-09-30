<?php

namespace App\Controller;

use App\Entity\Order;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrdersController extends Controller
{
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function list()
    {
        $orders = $this->getUser()->getOrders();

        return $this->render('orders/list.html.twig', ['orders' => $orders]);
    }

    /**
     * @ParamConverter("order", class="App\Entity\Order")
     * @param Order $order
     * @return Response
     */
    public function show(Order $order)
    {
        return $this->render('orders/details.html.twig', ['order' => $order]);
    }
}
