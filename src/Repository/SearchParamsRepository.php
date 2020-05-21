<?php

namespace App\Repository;

use App\Entity\SearchParams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SearchParams|null find($id, $lockMode = null, $lockVersion = null)
 * @method SearchParams|null findOneBy(array $criteria, array $orderBy = null)
 * @method SearchParams[]    findAll()
 * @method SearchParams[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchParamsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchParams::class);
    }

    // /**
    //  * @return SearchParams[] Returns an array of SearchParams objects
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
    public function findOneBySomeField($value): ?SearchParams
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
