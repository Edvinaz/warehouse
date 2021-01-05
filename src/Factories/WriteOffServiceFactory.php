<?php

declare(strict_types=1);

namespace App\Factories;

use App\Repository\WareDebitedMaterialsRepository;
use App\Repository\WareObjectsRepository;
use App\Repository\WarePurchasedMaterialsRepository;
use App\Repository\WareWriteOffsRepository;
use App\Services\WriteOffService;
use Doctrine\ORM\EntityManagerInterface;

class WriteOffServiceFactory
{
    private $entityManagerInterface;
    private $wareWriteOffRepository;
    private $objectsRepository;
    private $debitedMaterialsRepository;
    private $purchasedMaterialsRepository;

    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        WareWriteOffsRepository $wareWriteOffRepository,
        WareObjectsRepository $wareObjectsRepository,
        WareDebitedMaterialsRepository $wareDebitedMaterialsRepository,
        WarePurchasedMaterialsRepository $warePurchasedMaterialsRepository
    ) {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->wareWriteOffRepository = $wareWriteOffRepository;
        $this->objectsRepository = $wareObjectsRepository;
        $this->debitedMaterialsRepository = $wareDebitedMaterialsRepository;
        $this->purchasedMaterialsRepository = $warePurchasedMaterialsRepository;
    }

    public function createWriteOffServiceManager(): WriteOffService
    {
        return new WriteOffService(
            $this->wareWriteOffRepository,
            $this->objectsRepository,
            $this->debitedMaterialsRepository,
            $this->purchasedMaterialsRepository,
            $this->entityManagerInterface
        );
    }
}
