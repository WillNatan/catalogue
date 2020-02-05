<?php

namespace App\Repository;

use App\Entity\RefObjRapport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RefObjRapport|null find($id, $lockMode = null, $lockVersion = null)
 * @method RefObjRapport|null findOneBy(array $criteria, array $orderBy = null)
 * @method RefObjRapport[]    findAll()
 * @method RefObjRapport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RefObjRapportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefObjRapport::class);
    }

    // /**
    //  * @return RefObjRapport[] Returns an array of RefObjRapport objects
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
    public function findOneBySomeField($value): ?RefObjRapport
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
