<?php

namespace App\Repository;

use App\Entity\Transport\MonthlyUsage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MonthlyUsage|null find($id, $lockMode = null, $lockVersion = null)
 * @method MonthlyUsage|null findOneBy(array $criteria, array $orderBy = null)
 * @method MonthlyUsage[]    findAll()
 * @method MonthlyUsage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MonthlyUsageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MonthlyUsage::class);
    }

    public function save(MonthlyUsage $monthlyUsage)
    {
        $this->_em->persist($monthlyUsage);
        $this->_em->flush();
    }

    public function getCurrentUsage(\DateTimeInterface $date, int $transportId)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.date BETWEEN :begin AND :end')
            ->setParameter(':begin', $date->format('Y-m-01'))
            ->setParameter(':end', $date->format('Y-m-t'))
            ->andWhere('w.transport = :transport')
            ->setParameter('transport', $transportId)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }
}
