<?php

namespace App\Repository;

use App\Entity\ReportCatalog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ReportCatalog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportCatalog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportCatalog[]    findAll()
 * @method ReportCatalog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportCatalogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportCatalog::class);
    }


    public function findEntitiesByString($str){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT e
                FROM ReportCatalog::class e
                WHERE e.foo LIKE :str'
            )
            ->setParameter('str', '%'.$str.'%')
            ->getResult();
    }

    // /**
    //  * @return ReportCatalog[] Returns an array of ReportCatalog objects
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
    public function findOneBySomeField($value): ?ReportCatalog
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
