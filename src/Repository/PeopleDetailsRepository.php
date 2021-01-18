<?php

namespace App\Repository;

use App\Entity\Staff\PeopleDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PeopleDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method PeopleDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method PeopleDetails[]    findAll()
 * @method PeopleDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeopleDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PeopleDetails::class);
    }

    // /**
    //  * @return PeopleDetails[] Returns an array of PeopleDetails objects
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
    public function findOneBySomeField($value): ?PeopleDetails
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
