<?php

namespace App\Repository;

use App\Entity\Matrice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Matrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Matrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Matrice[]    findAll()
 * @method Matrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matrice::class);
    }

    // /**
    //  * @return Matrice[] Returns an array of Matrice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Matrice
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
