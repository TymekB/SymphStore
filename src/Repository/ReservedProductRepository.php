<?php

namespace App\Repository;

use App\Entity\ReservedProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ReservedProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReservedProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReservedProduct[]    findAll()
 * @method ReservedProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservedProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReservedProduct::class);
    }

//    /**
//     * @return ReservedProduct[] Returns an array of ReservedProduct objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReservedProduct
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
