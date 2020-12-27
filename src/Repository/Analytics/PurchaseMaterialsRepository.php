<?php

namespace App\Repository\Analytics;

use App\Helpers\DateInterval;
use App\Repository\WarePurchasedMaterialsRepository;

class PurchaseMaterialsRepository extends WarePurchasedMaterialsRepository
{
    public function getMonthPurchase(int $contrahent)
    {
        $date = new DateInterval();
        return $this->createQueryBuilder('p')
            ->join('p.invoice', 'i')
            ->andWhere('i.date BETWEEN :from AND :to')
            ->setParameter('from', $date->getBegin()->format('Y-m-d'))
            ->setParameter('to', $date->getEnd()->format('Y-m-d'))
            ->andWhere('i.contrahent = :contrahent')
            ->setParameter('contrahent', $contrahent)
            ->getQuery()
            ->getResult();
    }

    public function getInvoiceMaterials(int $invoiceId)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.invoice = :invoiceId')
            ->setParameter('invoiceId', $invoiceId)
            ->getQuery()
            ->getResult();
    }
}