<?php

namespace App\Repository\Analytics;

use App\Helpers\DateInterval;
use App\Repository\WareInvoicesRepository;

class PurchaseInvoicesRepository extends WareInvoicesRepository
{
    public function getMonthPurchases()
    {
        $date = new DateInterval();
        return $this->createQueryBuilder('i')
            ->join('i.contrahent', 'c')
            ->andWhere('i.date BETWEEN :from AND :to')
            ->setParameter('from', $date->getBegin()->format('Y-m-d'))
            ->setParameter('to', $date->getEnd()->format('Y-m-d'))
            ->select('c.name, c.id, SUM(i.amount) as amount')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }

    public function getContrahentMonthPurchases(int $contrahentId)
    {
        $date = new DateInterval();
        return $this->createQueryBuilder('i')
            ->andWhere('i.date BETWEEN :from AND :to')
            ->setParameter('from', $date->getBegin()->format('Y-m-d'))
            ->setParameter('to', $date->getEnd()->format('Y-m-d'))
            ->andWhere('i.contrahent = :contrahentId')
            ->setParameter('contrahentId', $contrahentId)
            // ->select('i.id, c.id, SUM(i.amount) as amount')
            // ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }
}