<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 05.09.18
 * Time: 22:17
 */

namespace App\ShoppingProcess\Cart;


use App\Entity\Product;
use App\Entity\ReservedProduct;
use App\Repository\ReservedProductRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProductReservator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var ReservedProductRepository
     */
    private $reservedProductRepository;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(EntityManagerInterface $em, ReservedProductRepository $reservedProductRepository, SessionInterface $session)
    {
        $this->em = $em;
        $this->reservedProductRepository = $reservedProductRepository;
        $this->session = $session;

        $this->session->start();
    }

    public function removeAll($minutes = 10)
    {
        $reservedProducts = $this->reservedProductRepository->findAll();

        foreach($reservedProducts as $reservedProduct) {

            $toTime = strtotime((new DateTime())->format("Y-m-d H:i:s"));
            $fromTime = strtotime($reservedProduct->getCreatedAt()->format("Y-m-d H:i:s"));

            $diff = round(($toTime - $fromTime) / 60, 2);

            if($diff >= $minutes) {
                $this->em->remove($reservedProduct);
            }

        }

        $this->em->flush();

        return true;
    }

    public function create(Product $product)
    {
        $reservedProduct = new ReservedProduct();

        $reservedProduct->setSessionId($this->session->getId());
        $reservedProduct->setProduct($product);
        $reservedProduct->setCreatedAt(new DateTime());

        $this->em->persist($reservedProduct);
        $this->em->flush();

        return true;
    }

}