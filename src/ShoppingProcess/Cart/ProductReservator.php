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
use App\ShoppingProcess\CartException\ProductNotInStockException;
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

    public function getAllByProduct(Product $product)
    {
        $productReservation = $this->reservedProductRepository->findAll(['product.id' => $product->getId()]);

        return $productReservation;
    }

    public function create(Product $product, $quantity = 1)
    {
        $reservedProduct = $this->em->getRepository(ReservedProduct::class)->findOneBy(
            [
                'product' => $product->getId(),
                'sessionId' => $this->session->getId()
            ]);

        if(!$reservedProduct) {

            $reservedProduct = new ReservedProduct();

            $reservedProduct->setSessionId($this->session->getId());
            $reservedProduct->setProduct($product);
            $reservedProduct->setCreatedAt(new DateTime());
            $reservedProduct->setQuantity(1);

            $this->em->persist($reservedProduct);
        }
        else {

            if($reservedProduct->getQuantity() + $quantity > $product->getQuantity()) {
                throw new ProductNotInStockException();
            }

            $reservedProduct->addQuantity($quantity);
            $reservedProduct->setCreatedAt(new DateTime());

            $this->em->persist($reservedProduct);
        }

        $this->em->flush();

        return true;
    }

    public function removeAllBySession()
    {
        $reservedProducts = $this->reservedProductRepository->findBy(['sessionId' => $this->session->getId()]);

        foreach($reservedProducts as $reservedProduct) {
            $this->em->remove($reservedProduct);
        }

        $this->em->flush();

        return true;
    }

    public function removeBySession(Product $product)
    {
        $reservedProduct = $this->reservedProductRepository->findOneBy(
            [
                'product' => $product->getId(),
                'sessionId' => $this->session->getId()
            ]);

        if(!$reservedProduct) {
            return false;
        }

        $this->em->remove($reservedProduct);
        $this->em->flush();

        return true;
    }

    public function removeAllByTimeLeft($minutes = 10)
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

}