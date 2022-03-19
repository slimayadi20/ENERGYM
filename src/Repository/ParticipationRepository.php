<?php

namespace App\Repository;

use App\Entity\Participation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Participation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participation[]    findAll()
 * @method Participation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participation::class);
    }
    public function findbyEvent($i)
    {

        return $this->createQueryBuilder('v')
            ->where(' i = 2  ')
            ->setParameter('i',$i)
            ->getQuery();
    }
    public function findUserinEvent($iduser,$id)
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.idUser','j')
            ->where(' j.id =:i ')
            ->setParameter('i',$iduser)
            ->leftJoin('v.idEvent','e')
            ->andWhere('e.id = :id') // : lezemetha les9a el parameter
            ->setParameter('id',$id)
            ->getQuery()
             ->execute();

           // ->andWhere(' :id MEMBER OF v.idEvent ')
           // ->innerJoin('v.idEvent','e')


    }
    // /**
    //  * @return Participation[] Returns an array of Participation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Participation
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
