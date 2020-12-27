<?php

namespace App\Repository\TimeCard;

use App\Helpers\DateInterval;
use App\Repository\TimeCardRepository;

class TimeCardSummary extends TimeCardRepository
{
    public function getList()
    {
        $date = new DateInterval();
        return $this->createQueryBuilder('w')
            ->andWhere('w.date BETWEEN :from AND :to')
            ->setParameter('from', $date->getBegin())
            ->setParameter('to', $date->getEnd())
            ->orderBy('w.date', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getForWorker(int $staff)
    {
        $date = new DateInterval();
        return $this->createQueryBuilder('w')
            ->andWhere('w.date BETWEEN :from AND :to')
            ->andWhere('w.staff = :staff')
            ->setParameter('from', $date->getBegin())
            ->setParameter('to', $date->getEnd())
            ->setParameter('staff', $staff)
            ->orderBy('w.date', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getWorkerDay(int $staff, string $date)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.date = :date')
            ->andWhere('w.staff = :staff')
            ->setParameter('from', $date)
            ->setParameter('staff', $staff)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }
}