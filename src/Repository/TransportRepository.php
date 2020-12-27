<?php

namespace App\Repository;

use App\Entity\Transport\Transport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Transport find($id, $lockMode = null, $lockVersion = null)
 * @method null|Transport findOneBy(array $criteria, array $orderBy = null)
 * @method Transport[]    findAll()
 * @method Transport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transport::class);
    }

    public function save(Transport $transport)
    {
        $this->_em->persist($transport);
        $this->_em->flush();
    }
}
