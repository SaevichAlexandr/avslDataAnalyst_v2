<?php

namespace App\Repository;

use App\Entity\Sd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sd|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sd|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sd[]    findAll()
 * @method Sd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sd::class);
    }

    // /**
    //  * @return Sd[] Returns an array of Sd objects
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
    public function findOneBySomeField($value): ?Sd
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
