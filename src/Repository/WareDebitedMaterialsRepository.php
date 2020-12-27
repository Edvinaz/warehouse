<?php

namespace App\Repository;

use App\Entity\Debition\WareDebitedMaterials;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|WareDebitedMaterials find($id, $lockMode = null, $lockVersion = null)
 * @method null|WareDebitedMaterials findOneBy(array $criteria, array $orderBy = null)
 * @method WareDebitedMaterials[]    findAll()
 * @method WareDebitedMaterials[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WareDebitedMaterialsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WareDebitedMaterials::class);
    }

    public function getWriteOffMaterialList(int $id)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.writeoff = :writeOff')
            ->setParameter('writeOff', $id)
            ->join('w.purchase', 'p')
            ->join('p.material', 'm')
            ->select('SUM(w.amount) as amount, SUM(w.amount * p.price)/SUM(w.amount) as price, m.name as name, m.unit as unit, m.id as materialID')
            ->groupBy('m.id')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getDebitedMaterialForUpdate(int $id)
    {
        return $this->createQueryBuilder('w')
            ->join('w.purchase', 'p')
            ->andWhere('p.material = :id')
            ->setParameter('id', $id)
            ->select('SUM(w.amount) as amount, SUM(w.amount * p.price)/SUM(w.amount) as price')
            ->groupBy('p.material')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getDebitedMaterial(int $materialId, int $writeOffId)
    {
        return $this->createQueryBuilder('w')
            ->join('w.purchase', 'p')
            ->andWhere('p.material = :id')
            ->setParameter('id', $materialId)
            ->andWhere('w.writeoff = :writeOff')
            ->setParameter('writeOff', $writeOffId)
            ->select('SUM(w.amount) as amount, SUM(w.amount * p.price)/SUM(w.amount) as price')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getDebitedMaterialList(int $materialId, int $writeOffId)
    {
        return $this->createQueryBuilder('w')
            ->join('w.purchase', 'p')
            ->andWhere('p.material = :id')
            ->setParameter('id', $materialId)
            ->andWhere('w.writeoff = :writeOff')
            ->setParameter('writeOff', $writeOffId)
            // ->select('SUM(w.amount) as amount, SUM(w.amount * p.price)/SUM(w.amount) as price')
            ->getQuery()
            ->getResult()
            ;
    }
}
