<?php
declare(strict_types=1);
namespace App\Services;

use App\Repository\WareInvoicesRepository;
use App\Repository\WareMaterialsRepository;
use App\Repository\WarePurchasedMaterialsRepository;
use Doctrine\ORM\EntityManagerInterface;

abstract class PurchaseService
{
    private $invoicesRepository;
    private $materialRepository;
    private $purchasedMaterialsRepository;
    private $em;

    public function __construct(
        WareInvoicesRepository $wareInvoicesRepository,
        WareMaterialsRepository $wareMaterialsRepository,
        WarePurchasedMaterialsRepository $warePurchasedMaterialsRepository,
        EntityManagerInterface $entityManagerInterface
    ) {
       $this->invoicesRepository = $wareInvoicesRepository;
       $this->materialRepository = $wareMaterialsRepository;
       $this->purchasedMaterialsRepository = $warePurchasedMaterialsRepository; 
       $this->em = $entityManagerInterface;
    }
}