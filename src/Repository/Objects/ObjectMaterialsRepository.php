<?php
namespace App\Repository\Objects;

use App\Repository\WarePurchasedMaterialsRepository;

class ObjectMaterialsRepository extends WarePurchasedMaterialsRepository
{
    // get's only reserved materials for selected Object, searches by material name, category and time purchased
    public function getObjectReservedMaterialsList(int $objectId, ?array $search)
    {        
        if (is_null($search)) {
            $search['search'] = '';
        }
        
        return $this->createQueryBuilder('w')
            ->andWhere('w.object = :id')
            ->setParameter('id', $objectId)
            ->andWhere('r.name LIKE :name')
            ->setParameter('name', '%'.$search['search'].'%')
            ->andWhere('i.date <= :date')
            ->setParameter('date', $this->to)
            ->andWhere('w.balance > 0')
            ->andWhere('c.type < 10')
            ->select('SUM(w.balance) as quantity, (SUM(w.balance * w.price)/SUM(w.balance)) as price, 
                                r.name as name, r.unit as unit, r.id as id')
            ->join('w.material', 'r')
            ->join('r.category', 'c')
            ->join('w.invoice', 'i')
            ->groupBy('w.material')
            ->getQuery()
            ->getResult()
            ;
    }
}