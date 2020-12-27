<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Objects\WareObjects;
use App\Entity\Purchases\WarePurchasedMaterials;
use App\Helpers\DateInterval;
use App\Helpers\DebitMaterialHelper;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @method null|WarePurchasedMaterials find($id, $lockMode = null, $lockVersion = null)
 * @method null|WarePurchasedMaterials findOneBy(array $criteria, array $orderBy = null)
 * @method WarePurchasedMaterials[]    findAll()
 * @method WarePurchasedMaterials[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WarePurchasedMaterialsRepository extends ServiceEntityRepository
{
    protected $entityManager;

    protected $from;
    protected $to;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, WarePurchasedMaterials::class);
        $this->entityManager = $em;
        $session = new Session();
        if (is_null($session->get('interval'))) {
            $this->from = new DateTime('now');
            $this->to = new DateTime('now');
        } else {
            $this->from = $session->get('interval')->getDate()->format('Y-m-01');
            $this->to = $session->get('interval')->getDate()->format('Y-m-t');
        }
    }

    // Gets purchased materials sum for selected invoice by $invoiceId
    public function getInvoiceMaterialsSum(int $invoiceId): float
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.invoice = :id')
            ->setParameter('id', $invoiceId)
            ->select('SUM(w.quantity * w.price) as sum')
            ->getQuery()
            ->getResult()
        ;
    }

    // get's only reserved materials for selected Object, searches by material name, category and time purchased
    public function getObjectReservedMaterials(WareObjects $object, ?array $search)
    {        
        if ($search['category']) {
            return $this->createQueryBuilder('w')
                ->andWhere('w.object = :id')
                ->setParameter('id', $object->getId())
                ->andWhere('r.name LIKE :name')
                ->setParameter('name', '%'.$search['search'].'%')
                ->andWhere('r.category = :category')
                ->setParameter('category', $search['category'])
                ->andWhere('i.date <= :date')
                ->setParameter('date', $this->to)
                ->andWhere('w.balance > 0')
                ->andWhere('c.type < 10')
                ->select('SUM(w.balance) as quantity, (SUM(w.balance * w.price)/SUM(w.balance)) as price, 
                                r.name as name, r.unit as unit, r.id as id')
                ->join('w.material', 'r')
                ->join('w.invoice', 'i')
                ->join('r.category', 'c')
                ->groupBy('w.material')
                ->getQuery()
                ->getResult()
            ;
        }
        
        return $this->createQueryBuilder('w')
            ->andWhere('w.object = :id')
            ->setParameter('id', $object->getId())
            ->andWhere('r.name LIKE :name')
            ->setParameter('name', '%'.$search['search'].'%')
            ->andWhere('i.date <= :date')
            ->setParameter('date', $this->to)
            ->andWhere('w.balance > 0')
            ->andWhere('c.type < 10')
            ->select('SUM(w.balance) as quantity, (SUM(w.balance * w.price)/SUM(w.balance)) as price, 
                                r.name as name, r.unit as unit, r.id as id')
            ->join('w.material', 'r')
            ->join('r.category', 'c')
            ->join('w.invoice', 'i')
            ->groupBy('w.material')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getNotReservedMaterials(?array $search)
    {
        if ($search['category']) {
            return $this->createQueryBuilder('w')
                ->andWhere('w.object is null')
                ->andWhere('r.name LIKE :name')
                ->setParameter('name', '%'.$search['search'].'%')
                ->andWhere('r.category = :category')
                ->setParameter('category', $search['category'])
                ->andWhere('i.date <= :date')
                ->setParameter('date', $this->to)
                ->andWhere('w.balance > 0')
                ->select('SUM(w.balance) as quantity, (SUM(w.balance * w.price)/SUM(w.balance)) as price, 
                                r.name as name, r.unit as unit, r.id as id')
                ->join('w.material', 'r')
                ->join('w.invoice', 'i')
                ->andWhere('c.type < 10')
                ->join('r.category', 'c')
                ->groupBy('w.material')
                ->getQuery()
                ->getResult()
            ;
        }

        return $this->createQueryBuilder('w')
            ->andWhere('w.object is null')
            ->andWhere('r.name LIKE :name')
            ->setParameter('name', '%'.$search['search'].'%')
            ->andWhere('i.date <= :date')
            ->setParameter('date', $this->to)
            ->andWhere('w.balance > 0')
            ->select('SUM(w.balance) as quantity, (SUM(w.balance * w.price)/SUM(w.balance)) as price, 
                                r.name as name, r.unit as unit, r.id as id')
            ->join('w.material', 'r')
            ->join('w.invoice', 'i')
            ->andWhere('c.type < 10')
            ->join('r.category', 'c')
            ->groupBy('w.material')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getReservedMaterialsForDebitting(DebitMaterialHelper $material)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.material = :material')
            ->setParameter('material', $material->getMaterial()->getId())
            ->andWhere('w.object = :object')
            ->setParameter('object', $material->getWriteOff()->getObject()->getId())
            ->andWhere('i.date <= :date')
            ->setParameter('date', $this->to)
            ->andWhere('w.balance > 0')
            ->join('w.invoice', 'i')
            ->orderBy('w.price', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getMaterialsForDebitting(DebitMaterialHelper $material)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.material = :material')
            ->setParameter('material', $material->getMaterial()->getId())
            ->andWhere('i.date <= :date')
            ->setParameter('date', $this->to)
            ->andWhere('w.object is null')
            ->andWhere('w.balance > 0')
            ->join('w.invoice', 'i')
            ->orderBy('w.price', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getObjectReservedMaterialsSum(int $objectId)
    {
        return $this->createQueryBuilder('w')
            ->join('w.material', 'm')
            ->join('m.category', 'c')
            ->andWhere('w.object = :object')
            ->andWhere('c.type < 10')
            ->setParameter('object', $objectId)
            ->select('SUM(w.balance * w.price) as sum')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getObjectServicesSum(int $objectId)
    {
        return $this->createQueryBuilder('w')
            ->join('w.material', 'm')
            ->join('m.category', 'c')
            ->andWhere('w.object = :object')
            ->andWhere('c.type = 20')
            ->setParameter('object', $objectId)
            ->select('SUM(w.balance * w.price) as sum')
            ->getQuery()
            ->getResult()
        ;
    }

    public function checkExistingPurchase(WarePurchasedMaterials $wpm)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.invoice = :invoice')
            ->andWhere('w.material = :material')
            ->andWhere('w.quantity = :quantity')
            ->andWhere('w.price = :price')
            ->setParameter('invoice', $wpm->getInvoice()->getId())
            ->setParameter('material', $wpm->getMaterial()->getId())
            ->setParameter('quantity', $wpm->getQuantity())
            ->setParameter('price', number_format(floatval($wpm->getPrice()), 5))
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get received fuel list on current month
     *
     * @return void
     */
    public function getFuelList()
    {
        $session = new Session();
        $month = $session->get('interval')->getDate();

        return $this->createQueryBuilder('w')
            ->join('w.material', 'm')
            ->join('m.category', 'c')
            ->join('w.invoice', 'i')
            ->andWhere('c.type = 2')
            // ->andWhere('w.balance = w.quantity')
            // ->andWhere('w.balance > 0')
            ->andWhere('i.date BETWEEN :from AND :to')
            ->setParameter(':from', $month->format('Y-m-01'))
            ->setParameter(':to', $month->format('Y-m-t'))
            ->getQuery()
            ->getResult()
        ;
    }

    public function persist(WarePurchasedMaterials $warePurchasedMaterials)
    {
        $this->_em->persist($warePurchasedMaterials);
    }

    public function save()
    {
        $this->_em->flush();
    }

    public function getObjectReservedMaterial(int $materialId, int $objectId)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.material = :material')
            ->andWhere('w.object = :object')
            ->setParameter(':material', $materialId)
            ->setParameter(':object', $objectId)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getAllObjectReservedMaterials(int $objectId)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.object = :object')
            ->setParameter(':object', $objectId)
            ->join('w.invoice', 'i')
            ->orderBy('i.date', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getMonthReservedMaterials(WareObjects $object, DateTimeInterface $date)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.object = :objectId')
            ->andWhere('i.date BETWEEN :from AND :to')
            ->setParameter('objectId', $object->getId())
            ->setParameter('from', $date->format('Y-m-01'))
            ->setParameter('to', $date->format('Y-m-t'))
            ->andWhere('w.balance > 0')
            ->select('SUM(w.balance) as quantity, (SUM(w.balance * w.price)/SUM(w.balance)) as price, 
                                r.name as name, r.unit as unit, r.id as id')
            ->join('w.material', 'r')
            ->join('w.invoice', 'i')
            ->andWhere('c.type < 10')
            ->join('r.category', 'c')
            ->groupBy('w.material')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getContrahentMaterialStatistics(int $contrahent)
    {
        $date = new DateInterval();
        return $this->createQueryBuilder('p')
            ->join('p.invoice', 'i')
            ->join('p.material', 'm')
            ->join('m.category', 'c')
            ->andWhere('i.contrahent = :contrahent AND i.date BETWEEN :from AND :to')
            ->setParameter('contrahent', $contrahent)
            ->setParameter('from', $date->getBegin())
            ->setParameter('to', $date->getEnd())
            ->select('SUM(p.quantity * p.price) as sum, c.type')
            ->groupBy('c.type')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getLastInserted()
    {
        return $this->createQueryBuilder('w')
            ->orderBy('w.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }
}
