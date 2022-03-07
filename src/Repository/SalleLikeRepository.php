<?php

namespace App\Repository;
use App\Entity\Salle;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\SalleLike;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SalleLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method SalleLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method SalleLike[]    findAll()
 * @method SalleLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalleLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SalleLike::class);
    }
    public function getCountForSalle(Salle $salle)
    {
        return $this->createQueryBuilder('l')
            ->select('COUNT(l) AS likes')
            ->andWhere('l.salle = :salle')
            ->setParameter('salle', $salle)
            ->getQuery()
            ->getSingleScalarResult();
    }
    // /**
    //  * @return SalleLike[] Returns an array of SalleLike objects
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
    public function findOneBySomeField($value): ?SalleLike
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