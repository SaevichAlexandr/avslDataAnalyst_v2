<?php

namespace App\Repository;

use App\Entity\SuppliersPrice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SuppliersPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuppliersPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuppliersPrice[]    findAll()
 * @method SuppliersPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuppliersPriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuppliersPrice::class);
    }

    // /**
    //  * @return SuppliersPrice[] Returns an array of SuppliersPrice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SuppliersPrice
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
