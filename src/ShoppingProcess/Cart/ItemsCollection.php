<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23.08.18
 * Time: 14:09
 */

namespace App\ShoppingProcess\Cart;


use App\ShoppingProcess\CartException\ItemNotFoundException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

class ItemsCollection extends ArrayCollection
{
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }

    public function addItem(Item $item): bool
    {
        $itemKey = $this->indexOf($item);

        if($itemKey !== false) {
            $this[$itemKey] = $item;
        }
        else {
            $this->add($item);
        }

        return true;
    }

    public function deleteItem(Item $item): bool
    {
        $key = $this->indexOf($item);

        $this->remove($key);

        return true;
    }

    public function searchItemByProductId(Int $id)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('productId', $id));

        $items = $this->matching($criteria);

        if($items->isEmpty()) {
            return false;
        }

        $key = $items->getKeys()[0];

        return $items[$key];
    }

    public function getProductsId(): array
    {
        $productsId = [];

        foreach($this as $item) {
            $productsId[] = $item->getProductId();
        }

        return $productsId;
    }

}