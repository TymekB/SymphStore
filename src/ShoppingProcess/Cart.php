<?php
/**
 * Created by PhpStorm.
 * User: tymek
 * Date: 07.07.18
 * Time: 19:52
 */

namespace App\ShoppingProcess;


use App\Entity\Product;
use App\Entity\User;
use App\Repository\ItemRepository;
use App\ShoppingProcess\Cart\Decorators\ItemsProductDecorator;
use App\Entity\Item;
use App\ShoppingProcess\CartException\ItemNotFoundException;
use App\ShoppingProcess\CartException\ProductNotInStockException;
use App\ShoppingProcess\CartException\ProductsSizeIsNotEqualItemsSizeException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Cart
{
    /**
     * @var ItemsProductDecorator
     */
    private $itemsProductDecorator;
    /**
     * @var ItemRepository
     */
    private $itemRepository;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session, EntityManagerInterface $em, ItemRepository $itemRepository, ItemsProductDecorator $itemsProductDecorator)
    {
        $this->session = $session;
        $this->session->start();
        $this->itemsProductDecorator = $itemsProductDecorator;
        $this->itemRepository = $itemRepository;
        $this->em = $em;
    }

    public function getItems()
    {
        $items = $this->itemRepository->findBy(['sessionId' => $this->session->getId()]);

        return $items;
    }

    public function getTotalAmount()
    {
        $items = $this->getItems();
        $total = 0;

        if(!$items) {
            return $total;
        }

        $total = array_map(function(Item $item) {
            return $item->getProduct()->getPrice() * $item->getQuantity();
        }, $items);

        return array_sum($total);
    }

    public function addProduct(Product $product, int $quantity = 1)
    {
        if($product->getQuantity() < $quantity) {
            throw new ProductNotInStockException();
        }

        $item = $this->itemRepository->findOneByProductAndSessionId($product, $this->session->getId());

        if(!$item) {
            $item = new Item();
            $item->setProduct($product)
                ->setQuantity($quantity)
                ->setSessionId($this->session->getId());
        }
        else {

            if($item->getQuantity() + $quantity > $product->getQuantity()) {
                throw new ProductNotInStockException();
            }

            $item->addQuantity($quantity);
        }

        $this->em->persist($item);
        $this->em->flush();


        return true;
    }

    /**
     * @param array $products
     * @return bool
     * @throws ProductsSizeIsNotEqualItemsSizeException
     * @throws ProductNotInStockException
     */
    public function updateProducts(array $products): bool
    {
        $items = $this->getItems();

        if (count($products) != count($items)) {
            throw new ProductsSizeIsNotEqualItemsSizeException();
        }

        foreach($items as $item) {

                foreach($products as $product) {

                    if ($product->id == $item->getProduct()->getId() && $product->price == $item->getProduct()->getPrice()) {

                        if($item->getProduct()->getQuantity() < $product->quantity || $product->quantity <= 0) {

                            throw new ProductNotInStockException($item->getProduct()->getName(). " not in stock.");
                        }

                        $item->setQuantity($product->quantity);
                        $this->em->persist($item);
                    }
                }
        }

        $this->em->flush();

        return true;
    }

    public function deleteProduct(Product $product)
    {
        $item = $this->itemRepository->findOneByProductAndSessionId($product, $this->session->getId());

        if(!$item) {
            throw new ItemNotFoundException();
        }

        $this->em->remove($item);
        $this->em->flush();

        return true;
    }

    public function removeItems()
    {
        $query = $this->em->createQueryBuilder()
            ->delete()
            ->from(Item::class, 'i')
            ->where("i.sessionId = :sessionId")
            ->setParameter("sessionId", $this->session->getId())
            ->getQuery();

        $query->execute();

        return true;
    }
}