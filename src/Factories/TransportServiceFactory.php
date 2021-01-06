<?php

declare(strict_types=1);

namespace App\Factories;

use App\Repository\MonthlyUsageRepository;
use App\Repository\TransportRepository;
use App\Repository\WarePurchasedMaterialsRepository;
use App\Services\TransportService;

class TransportServiceFactory
{
    private $repository;
    private $monthlyUsageRepository;
    private $fuelPurchaseRepository;

    public function __construct(
        TransportRepository $transportRepository,
        MonthlyUsageRepository $monthlyUsageRepository,
        WarePurchasedMaterialsRepository $fuelPurchaseRepository
    ) {
        $this->repository = $transportRepository;
        $this->monthlyUsageRepository = $monthlyUsageRepository;
        $this->fuelPurchaseRepository = $fuelPurchaseRepository;
    }

    public function createTransportServiceManager(): TransportService
    {
        return new TransportService(
            $this->repository,
            $this->monthlyUsageRepository,
            $this->fuelPurchaseRepository
        );
    }
}
