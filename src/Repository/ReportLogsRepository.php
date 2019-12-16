<?php

namespace App\Repository;

use App\Entity\ReportLogs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ReportLogs|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportLogs|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportLogs[]    findAll()
 * @method ReportLogs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportLogsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportLogs::class);
    }

    // /**
    //  * @return ReportLogs[] Returns an array of ReportLogs objects
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
    public function findOneBySomeField($value): ?ReportLogs
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
