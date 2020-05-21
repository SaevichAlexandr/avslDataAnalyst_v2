<?php

namespace App\Repository;

use App\Entity\RawData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RawData|null find($id, $lockMode = null, $lockVersion = null)
 * @method RawData|null findOneBy(array $criteria, array $orderBy = null)
 * @method RawData[]    findAll()
 * @method RawData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RawDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RawData::class);
    }

    // /**
    //  * @return RawData[] Returns an array of RawData objects
    //  */
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
    public function findOneBySomeField($value): ?RawData
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
