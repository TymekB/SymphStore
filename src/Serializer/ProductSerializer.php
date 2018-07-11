<?php
/**
 * Created by PhpStorm.
 * User: tymek
 * Date: 10.07.18
 * Time: 11:28
 */

namespace App\Serializer;


use App\Entity\Product;
use Symfony\Component\Serializer\SerializerInterface;

class ProductSerializer
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * ProductSerializer constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function normalize(Product $product)
    {
        $data = $this->serializer->normalize($product, null, ['attributes' => ['id', 'name', 'price']]);

        return $data;
    }
}