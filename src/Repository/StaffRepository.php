<?php

namespace App\Repository;

use App\Entity\Staff\Staff;
use App\Helpers\DateInterval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Staff|null find($id, $lockMode = null, $lockVersion = null)
 * @method Staff|null findOneBy(array $criteria, array $orderBy = null)
 * @method Staff[]    findAll()
 * @method Staff[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StaffRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Staff::class);
    }

    /**
     * @return Staff[]
     */
    public function getAvailableStaff()
    {
        $date = new DateInterval();
        return $this->createQueryBuilder('w')
            ->andWhere('w.responsible = 0')
            // ->orWhere('w.responsible = 0 AND w.fired <= :to AND w.accepted >= :from')
            // ->setParameter('to', $date->getEnd()->format('Y-m-d'))
            // ->setParameter('from', $date->getBegin()->format('Y-m-d'))
            ->getQuery()
            ->getResult()
            ;
    }
}
