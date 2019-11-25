<?php

namespace App\Repository;

use App\Entity\SousDossier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SousDossier|null find($id, $lockMode = null, $lockVersion = null)
 * @method SousDossier|null findOneBy(array $criteria, array $orderBy = null)
 * @method SousDossier[]    findAll()
 * @method SousDossier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SousDossierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SousDossier::class);
    }

    // /**
    //  * @return SousDossier[] Returns an array of SousDossier objects
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
    public function findOneBySomeField($value): ?SousDossier
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
