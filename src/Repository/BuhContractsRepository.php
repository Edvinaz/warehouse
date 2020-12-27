<?php

namespace App\Repository;

use App\Entity\Sales\BuhContracts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BuhContracts|null find($id, $lockMode = null, $lockVersion = null)
 * @method BuhContracts|null findOneBy(array $criteria, array $orderBy = null)
 * @method BuhContracts[]    findAll()
 * @method BuhContracts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuhContractsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuhContracts::class);
    }

    public function save(BuhContracts $contract)
    {
        $this->_em->persist($contract);
        $this->_em->flush();
    }
}
