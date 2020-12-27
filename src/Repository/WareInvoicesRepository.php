<?php

namespace App\Repository;

use App\Entity\Purchases\WareInvoices;
use App\Helpers\DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @method null|WareInvoices find($id, $lockMode = null, $lockVersion = null)
 * @method null|WareInvoices findOneBy(array $criteria, array $orderBy = null)
 * @method WareInvoices[]    findAll()
 * @method WareInvoices[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WareInvoicesRepository extends ServiceEntityRepository
{
    protected $em;
    protected $params;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em, ParameterBagInterface $param)
    {
        parent::__construct($registry, WareInvoices::class);
        $this->em = $em;
        $this->params = $param;
    }

    public function invoicesSearch(string $search)
    {
        $date = new DateInterval();
        if ('' === $search) {
            return $this->createQueryBuilder('w')
                ->andWhere('w.date BETWEEN :from AND :to')
                ->setParameter('from', $date->getBegin())
                ->setParameter('to', $date->getEnd())
                ->getQuery()
                ->getResult()
                ;
        }

        return $this->createQueryBuilder('w')
            ->andWhere('w.number LIKE :search')
            ->andWhere('w.date BETWEEN :from AND :to')
            ->setParameter('from', $date->getBegin())
            ->setParameter('to', $date->getEnd())
            ->setParameter('search', '%'.$search.'%')
            ->getQuery()
            ->getResult()
                ;
    }

    public function invoiceList(string $search)
    {
        $date = new DateInterval();
        $query = $this->createQueryBuilder('w')
            ->join('w.contrahent', 'c')
            ->andWhere('w.date BETWEEN :from AND :to')
            ->andWhere('w.number LIKE :search')
            ->orWhere('c.name LIKE :search AND w.date BETWEEN :from AND :to')
            ->setParameter('from', $date->getBegin())
            ->setParameter('to', $date->getEnd())
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('w.date', 'ASC')
            ;

        return new Paginator($query, $fetchJoinCollection = true);
    }

    public function save(WareInvoices $invoice)
    {
        $this->em->persist($invoice);
        $this->em->flush();
    }

    public function invoiceListStatistic()
    {
        $date = new DateInterval();
        return $this->createQueryBuilder('w')
            ->join('w.contrahent', 'c')
            ->andWhere('w.date BETWEEN :from AND :to')
            ->setParameter('from', $date->getBegin())
            ->setParameter('to', $date->getEnd())
            ->select('c.name, c.id')
            ->groupBy('w.contrahent')
            ->getQuery()
            ->getResult()
            ;
    }
}
