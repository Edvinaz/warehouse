<?php

namespace App\Repository;

use App\Entity\Materials\WareMaterialCategories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WareMaterialCategories|null find($id, $lockMode = null, $lockVersion = null)
 * @method WareMaterialCategories|null findOneBy(array $criteria, array $orderBy = null)
 * @method WareMaterialCategories[]    findAll()
 * @method WareMaterialCategories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WareMaterialCategoriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WareMaterialCategories::class);
    }

    public function getMaterialCategories()
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.type < 10')
            ->getQuery()
            ->getResult()
        ;
    }
}
