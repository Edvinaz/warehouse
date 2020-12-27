<?php

namespace App\Repository;

use App\Entity\Sales\BuhInvoices;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|BuhInvoices find($id, $lockMode = null, $lockVersion = null)
 * @method null|BuhInvoices findOneBy(array $criteria, array $orderBy = null)
 * @method BuhInvoices[]    findAll()
 * @method BuhInvoices[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuhInvoicesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuhInvoices::class);
    }

    public function save(BuhInvoices $invoice)
    {
        $this->_em->persist($invoice);
        $this->_em->flush();

        return $invoice;
    }

    public function getLastInvoice()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.number', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }
}
