<?php

namespace App\Repository;

use App\Entity\Sales\BuhInvoiceContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|BuhInvoiceContent find($id, $lockMode = null, $lockVersion = null)
 * @method null|BuhInvoiceContent findOneBy(array $criteria, array $orderBy = null)
 * @method BuhInvoiceContent[]    findAll()
 * @method BuhInvoiceContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuhInvoiceContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuhInvoiceContent::class);
    }

    public function save(BuhInvoiceContent $content)
    {
        $this->_em->persist($content);
        $this->_em->flush();
    }

    public function delete(BuhInvoiceContent $content)
    {
        $this->_em->remove($content);
        $this->_em->flush();
    }

    public function getObjectIncomeSum(int $objectId)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.object = :object')
            ->setParameter('object', $objectId)
            ->select('SUM(w.amount * w.price) as sum')
            ->getQuery()
            ->getResult()
        ;
    }
}
