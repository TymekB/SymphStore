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
    const DEFAULT_LIVE_TIME = 10;
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

    public function updateReservations(array $products)
    {
        $productsId = [];

        foreach($products as $product) {
            $productsId[] = $product->id;
        }

        $reservedProducts = $this->em->getRepository(ReservedProduct::class)->findBy(
            [
                'product' => $productsId,
                'sessionId' => $this->session->getId()
            ]
        );

        foreach($reservedProducts as $reservedProduct) {

            foreach($products as $product) {

                if($product->id == $reservedProduct->getProduct()->getId() && $product->quantity > 0) {

                    $reservedProduct->setQuantity($product->quantity);
                    $reservedProduct->setCreatedAt(new DateTime());

                    $this->em->persist($reservedProduct);
                }
            }
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

    public function purgeOld(int $minutes = self::DEFAULT_LIVE_TIME)
    {
        $query = $this->em->createQueryBuilder()
            ->delete()
            ->from(ReservedProduct::class, 'rp')
            ->where("CURRENT_TIMESTAMP() - rp.createdAt >= :time")
            ->setParameter("time", $minutes)->getQuery();

        $query->execute();

        return true;
    }

}