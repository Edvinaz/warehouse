<?php

namespace App\Repository;

use App\Entity\Objects\WareObjects;
use App\Helpers\DateInterval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * @method null|WareObjects find($id, $lockMode = null, $lockVersion = null)
 * @method null|WareObjects findOneBy(array $criteria, array $orderBy = null)
 * @method WareObjects[]    findAll()
 * @method WareObjects[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WareObjectsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WareObjects::class);
    }

    public function getLastNumber(): int
    {
        $number = 0;

        try {
            $number = $this->createQueryBuilder('e')
                ->select('MAX(e.number)')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        } catch (NonUniqueResultException $e) {
        }

        return $number[1] + 1;
    }

    public function getObjectsList(?string $status)
    {
        $date = new DateInterval();
        $query = $this->createQueryBuilder('e')
                    ->andWhere('e.date <= :date')
                    ->andWhere('e.status NOT LIKE :status')
                    ->setParameter('date', $date->getEnd())
                    ->setParameter('status', 'CLOSED');

        if (!is_null($status)) {
            $query->andWhere('e.status = :status')
                ->setParameter('status', $status);
        }

        return $query->orderBy('e.number', 'DESC')
        ->getQuery()
        ->getResult()
        ;   
    }

    public function save(WareObjects $object): void
    {
        try {
            $this->_em->persist($object);
        } catch (ORMException $e) {
        }

        try {
            $this->_em->flush();
        } catch (OptimisticLockException $e) {
        } catch (ORMException $e) {
        }
    }

    public function deleteObject(WareObjects $object): void
    {
        $this->_em->remove($object);
        $this->_em->flush();
    }
}
