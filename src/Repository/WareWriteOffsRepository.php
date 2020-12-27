<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Debition\WareWriteOffs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|WareWriteOffs find($id, $lockMode = null, $lockVersion = null)
 * @method null|WareWriteOffs findOneBy(array $criteria, array $orderBy = null)
 * @method WareWriteOffs[]    findAll()
 * @method WareWriteOffs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WareWriteOffsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WareWriteOffs::class);
    }

    public function getList(string $from, string $to)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.date BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getResult()
            ;
    }

    public function checkWriteOff(string $date)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.date = :date')
            ->setParameter('date', $date)
            ->andWhere('w.invoice is null')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }

    public function save(WareWriteOffs $writeOff)
    {
        $this->_em->persist($writeOff);
        $this->_em->flush();

        return $writeOff;
    }

    public function getObjectDebitedSum(int $objectId)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.object = :object')
            ->setParameter('object', $objectId)
            ->select('SUM(w.amount) as sum')
            ->getQuery()
            ->getResult()
        ;
    }
}
