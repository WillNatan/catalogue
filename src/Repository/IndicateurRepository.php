<?php

namespace App\Repository;

use App\Entity\Indicateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Indicateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Indicateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Indicateur[]    findAll()
 * @method Indicateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndicateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Indicateur::class);
    }

    // /**
    //  * @return Indicateur[] Returns an array of Indicateur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Indicateur
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
