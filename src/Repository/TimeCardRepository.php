<?php

namespace App\Repository;

use App\Entity\Objects\TimeCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use DateTimeInterface;

/**
 * @method TimeCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeCard[]    findAll()
 * @method TimeCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeCard::class);
    }

    public function getWorkedHours($date, int $worker)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.date = :date')
            ->setParameter('date', $date)
            ->andWhere('w.staff = :worker')
            ->setParameter('worker', $worker)
            ->groupBy('w.date')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getWorkedDays(int $workerID, DateTimeInterface $date)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.date BETWEEN :from AND :to')
            ->andWhere('w.staff = :worker')
            ->setParameter('from', $date->format('Y-m-01'))
            ->setParameter('to', $date->format('Y-m-t'))
            ->setParameter('worker', $workerID)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getWorkedDay(int $workerId, string $date)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.staff = :worker')
            ->andWhere('w.date = :date')
            ->setParameter('worker', $workerId)
            ->setParameter('date', $date)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }

    public function save(TimeCard $card)
    {
        $this->_em->persist($card);
        $this->_em->flush();
    }
}
