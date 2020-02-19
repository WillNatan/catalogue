<?php

namespace App\Repository;

use App\Entity\ReferentielObjets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ReferentielObjets|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReferentielObjets|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReferentielObjets[]    findAll()
 * @method ReferentielObjets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReferentielObjetsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReferentielObjets::class);
    }

    public function findByIndicateurs()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.nomObjet', 'ASC')
            ->where('u.qualification = :qualification')
            ->setParameter('qualification','indicateur')
            ->getQuery()
            ->execute()
            ;
    }

    public function findByAxes()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.nomObjet', 'ASC')
            ->where('u.qualification = :qualification')
            ->setParameter('qualification',"axe d'analyse")
            ->getQuery()
            ->execute()
            ;
    }

    // /**
    //  * @return ReferentielObjets[] Returns an array of ReferentielObjets objects
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
    public function findOneBySomeField($value): ?ReferentielObjets
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
