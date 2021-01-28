<?php

namespace App\Repository;

use App\Helpers\DateInterval;
use App\Entity\WarehouseReview;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method WarehouseReview|null find($id, $lockMode = null, $lockVersion = null)
 * @method WarehouseReview|null findOneBy(array $criteria, array $orderBy = null)
 * @method WarehouseReview[]    findAll()
 * @method WarehouseReview[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WarehouseReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WarehouseReview::class);
    }

    public function getMonthRewiev()
    {
        $date = new DateInterval();
        return $this->findOneBy(['month' => $date->getDayDate(1)]);
    }

    // /**
    //  * @return WarehouseReview[] Returns an array of WarehouseReview objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WarehouseReview
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
