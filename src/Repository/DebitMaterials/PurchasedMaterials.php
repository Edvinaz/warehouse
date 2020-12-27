<?php
declare(strict_types=1);

namespace App\Repository\DebitMaterials;

use App\Entity\Purchases\WarePurchasedMaterials;
use App\Repository\WarePurchasedMaterialsRepository;
use Doctrine\ORM\NonUniqueResultException;

class PurchasedMaterials extends WarePurchasedMaterialsRepository
{
    /**
     * @param int $materialId
     * @return array
     * @throws NonUniqueResultException
     */
    public function getMaterialForDebit(int $materialId): ?array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.material = :materialId')
            ->setParameter('materialId', $materialId)
            ->andWhere('i.date <= :date')
            ->setParameter('date', $this->to)
            ->andWhere('a.object is null')
            ->andWhere('a.balance > 0')
            ->select('SUM(a.balance) as quantity, SUM(a.balance * a.price)/SUM(a.balance) as price')
            ->join('a.invoice', 'i')
            ->groupBy('a.material')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param int $materialId
     * @param int $objectId
     * @return array
     * @throws NonUniqueResultException
     */
    public function getReservedMaterialForDebit(int $materialId, int $objectId): ?array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.material = :materialId')
            ->setParameter('materialId', $materialId)
            ->andWhere('a.object = :objectId')
            ->setParameter('objectId', $objectId)
            ->andWhere('i.date <= :date')
            ->setParameter('date', $this->to)
            ->andWhere('a.balance > 0')
            ->select('SUM(a.balance) as quantity, SUM(a.balance * a.price)/SUM(a.balance) as price')
            ->join('a.invoice', 'i')
            ->groupBy('a.material')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}