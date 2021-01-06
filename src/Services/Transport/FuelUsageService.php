<?php
declare(strict_types=1);

namespace App\Services\Transport;

use App\Entity\Transport\MonthlyUsage;
use App\Repository\MonthlyUsageRepository;

class FuelUsageService
{
    protected $usageRepository;

    public function __construct(
        MonthlyUsageRepository $usageRepository
    ) {
       $this->usageRepository = $usageRepository;
    }

    public function getTransportMonthlyUsageClass(int $transportId, \DateTimeInterface $date)
    {
        return $this->usageRepository->getCurrentUsage($date, $transportId);
    }

    public function saveMonthlyUsage(MonthlyUsage $monthlyUsage)
    {
        $this->usageRepository->save($monthlyUsage);
    }

}