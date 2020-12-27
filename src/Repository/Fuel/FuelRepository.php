<?php

namespace App\Repository\Fuel;

use App\Repository\WarePurchasedMaterialsRepository;
use DateTimeInterface;

class FuelRepository extends WarePurchasedMaterialsRepository
{
    public function getMonthStatistic(DateTimeInterface $date)
    {
        return $this->createQueryBuilder('w')
            ->join('w.material', 'm')
            ->join('m.category', 'c')
            ->join('w.invoice', 'i')
            ->andWhere('c.type = 2')
            ->andWhere('i.date BETWEEN :from AND :to')
            ->setParameter(':from', $date->format('Y-m-01'))
            ->setParameter(':to', $date->format('Y-m-t'))
            ->select('SUM(w.quantity) as total, c.name as category')
            ->groupBy('m.category')
            ->getQuery()
            ->getResult()
        ;
    }
}