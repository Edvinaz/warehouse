<?php

namespace App\Repository;

use App\Entity\Materials\WareMaterials;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method null|WareMaterials find($id, $lockMode = null, $lockVersion = null)
 * @method null|WareMaterials findOneBy(array $criteria, array $orderBy = null)
 * @method WareMaterials[]    findAll()
 * @method WareMaterials[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WareMaterialsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WareMaterials::class);
    }

    public function findByName(string $name): ?array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.name LIKE :name')
            ->setParameter('name', '%'.$name.'%')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getPaginatedMaterialsList(string $search)
    {
        $query = $this->createQueryBuilder('w')
            ->andWhere('w.name LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->join('w.category', 'c')
            ->andWhere('c.type < 30')
            ->setFirstResult(0)
            ->setMaxResults(25)
        ;

        return new Paginator($query, $fetchJoinCollection = true);
    }
}
