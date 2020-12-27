<?php

namespace App\Repository\Analytics;

use App\Helpers\DateInterval;
use App\Repository\WareObjectsRepository;

class ObjectsRepository extends WareObjectsRepository
{
    public function getYearObjects()
    {
        $date = new DateInterval();
        return $this->createQueryBuilder('o')
            ->andWhere('o.date BETWEEN :from and :to')
            ->setParameter('from', $date->getYear().'-01-01')
            ->setParameter('to', $date->getYear().'-12-31')
            ->orderBy('o.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getContrahentYearObjects(int $contrahentId)
    {
        $date = new DateInterval();
        return $this->createQueryBuilder('o')
            ->andWhere('o.date BETWEEN :from and :to')
            ->setParameter('from', $date->getYear().'-01-01')
            ->setParameter('to', $date->getYear().'-12-31')
            ->andWhere('o.contrahent = :contrahentId')
            ->setParameter('contrahentId', $contrahentId)
            ->orderBy('o.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
