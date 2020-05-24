<?php

namespace App\Repository;

use App\Entity\OfferData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OfferData|null find($id, $lockMode = null, $lockVersion = null)
 * @method OfferData|null findOneBy(array $criteria, array $orderBy = null)
 * @method OfferData[]    findAll()
 * @method OfferData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfferDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OfferData::class);
    }

    // /**
    //  * @return OfferData[] Returns an array of OfferData objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OfferData
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
