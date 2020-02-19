<?php

namespace App\Repository;

use App\Entity\Liens;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Liens|null find($id, $lockMode = null, $lockVersion = null)
 * @method Liens|null findOneBy(array $criteria, array $orderBy = null)
 * @method Liens[]    findAll()
 * @method Liens[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LiensRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Liens::class);
    }

    // /**
    //  * @return Liens[] Returns an array of Liens objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Liens
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
