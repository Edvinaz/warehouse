<?php

namespace App\Repository;

use App\Entity\Contrahents;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contrahents|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contrahents|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contrahents[]    findAll()
 * @method Contrahents[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContrahentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contrahents::class);
    }
}
