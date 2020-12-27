<?php

namespace App\Repository;

use App\Entity\Transport\FuelReceived2019;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FuelReceived2019|null find($id, $lockMode = null, $lockVersion = null)
 * @method FuelReceived2019|null findOneBy(array $criteria, array $orderBy = null)
 * @method FuelReceived2019[]    findAll()
 * @method FuelReceived2019[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FuelReceived2019Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FuelReceived2019::class);
    }

    public function getFuelReceived(\DateTimeInterface $date)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.date BETWEEN :from AND :to')
            ->setParameter('from', $date->format('Y-m-01'))
            ->setParameter('to', $date->format('Y-m-t'))
            ->orderBy('w.date', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getAllMonthFuel(\DateTimeInterface $date, int $transportId)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.date BETWEEN :from AND :to')
            ->setParameter('from', $date->format('Y-m-01'))
            ->setParameter('to', $date->format('Y-m-t'))
            ->andWhere('w.transport=:transport')
            ->setParameter('transport', $transportId)
            ->orderBy('w.date', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function save(FuelReceived2019 $fuelReceived2019)
    {
        $this->_em->persist($fuelReceived2019);
        $this->_em->flush();
    }
}
