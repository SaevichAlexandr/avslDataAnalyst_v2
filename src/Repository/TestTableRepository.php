<?php

namespace App\Repository;

use App\Entity\TestTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TestTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestTable[]    findAll()
 * @method TestTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestTable::class);
    }

    // /**
    //  * @return TestTable[] Returns an array of TestTable objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TestTable
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
