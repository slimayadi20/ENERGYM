<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reclamation[]    findAll()
 * @method Reclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    public function triStatusASC(){
        return $this->createQueryBuilder('u')
            ->orderBy('u.statut','ASC')
            ->getQuery()
            ->getResult();
    }

    public function triStatusDESC(){
        return $this->createQueryBuilder('p')
            ->orderBy('p.statut','DESC')
            ->getQuery()
            ->getResult();
    }
    public function triDateASC(){
        return $this->createQueryBuilder('r')
            ->orderBy('r.DateCreation','ASC')
            ->getQuery()
            ->getResult();
    }

    public function triDateDESC(){
        return $this->createQueryBuilder('e')
            ->orderBy('e.DateCreation','DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Reclamation[] Returns an array of Reclamation objects
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
    public function findOneBySomeField($value): ?Reclamation
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
