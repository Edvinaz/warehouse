<?php

declare(strict_types=1);

namespace App\Factories;

use App\Repository\WareInvoicesRepository;
use App\Repository\WareMaterialsRepository;
use App\Repository\WarePurchasedMaterialsRepository;
use App\Services\PurchaseService;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseServiceFactory
{
    private $invoicesRepository;
    private $materialRepository;
    private $purchasedMaterialsRepository;
    private $entityManager;

    public function __construct(
        WareInvoicesRepository $invoicesRepository,
        WareMaterialsRepository $materialRepository,
        WarePurchasedMaterialsRepository $purchasedMaterialsRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->invoicesRepository = $invoicesRepository;
        $this->materialRepository = $materialRepository;
        $this->purchasedMaterialsRepository = $purchasedMaterialsRepository;
        $this->entityManager = $entityManager;
    }

    public function createPurchaseServiceManager(): PurchaseService
    {
        return new PurchaseService(
            $this->invoicesRepository,
            $this->materialRepository,
            $this->purchasedMaterialsRepository,
            $this->entityManager
        );
    }
}
