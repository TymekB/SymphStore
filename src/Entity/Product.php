<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @Vich\Uploadable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     *
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $img;

    /**
     * @Vich\UploadableField(mapping="product_images", fileNameProperty="image")
     * @var File
     */

    private $imageFile;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Discount", inversedBy="products")
     */
    private $discount;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderedProduct", mappedBy="product")
     */
    private $orderedProducts;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReservedProduct", mappedBy="product", orphanRemoval=true)
     */
    private $reservedProducts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Item", mappedBy="product", orphanRemoval=true)
     */
    private $items;

    public function __construct()
    {
        $this->orderedProducts = new ArrayCollection();
        $this->reservedProducts = new ArrayCollection();
        $this->items = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        $price = $this->price / 100;

        if(!$this->discount) {
            return $price;
        }

        $discountedPrice = $price * ((100 - $this->discount->getPercent()) / 100);

        return round($discountedPrice, 2, PHP_ROUND_HALF_UP);
    }

    public function setPrice(Float $price): self
    {
        $this->price = $price * 100;

        return $this;
    }

    public function getNotDiscountedPrice()
    {
        $price = $this->price / 100;

        return $price;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function setImg($img)
    {
        $this->img = $img;
    }

    public function getImg()
    {
        return $this->img;
    }

    public function setImage($image)
    {
        $this->img = $image;
    }

    public function getImage()
    {
        return $this->img;
    }

    /**
     * @param File $imageFile
     */
    public function setImageFile(File $imageFile): void
    {
        $this->imageFile = $imageFile;
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function getDiscount(): ?Discount
    {
        return $this->discount;
    }

    public function setDiscount(?Discount $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return Collection|OrderedProduct[]
     */
    public function getOrderedProducts(): Collection
    {
        return $this->orderedProducts;
    }

    public function addOrderedProduct(OrderedProduct $orderedProduct): self
    {
        if (!$this->orderedProducts->contains($orderedProduct)) {
            $this->orderedProducts[] = $orderedProduct;
            $orderedProduct->setProduct($this);
        }

        return $this;
    }

    public function removeOrderedProduct(OrderedProduct $orderedProduct): self
    {
        if ($this->orderedProducts->contains($orderedProduct)) {
            $this->orderedProducts->removeElement($orderedProduct);
            // set the owning side to null (unless already changed)
            if ($orderedProduct->getProduct() === $this) {
                $orderedProduct->setProduct(null);
            }
        }

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return Collection|ReservedProduct[]
     */
    public function getReservedProducts(): Collection
    {
        return $this->reservedProducts;
    }

    public function addReservedProduct(ReservedProduct $reservedProduct): self
    {
        if (!$this->reservedProducts->contains($reservedProduct)) {
            $this->reservedProducts[] = $reservedProduct;
            $reservedProduct->setProduct($this);
        }

        return $this;
    }

    public function removeReservedProduct(ReservedProduct $reservedProduct): self
    {
        if ($this->reservedProducts->contains($reservedProduct)) {
            $this->reservedProducts->removeElement($reservedProduct);
            // set the owning side to null (unless already changed)
            if ($reservedProduct->getProduct() === $this) {
                $reservedProduct->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Item[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setProduct($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getProduct() === $this) {
                $item->setProduct(null);
            }
        }

        return $this;
    }
}
