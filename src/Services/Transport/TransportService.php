<?php

declare(strict_types=1);

namespace App\Services\Transport;

use App\Entity\Transport\Transport;
use App\Repository\FuelReceived2019Repository;
use App\Repository\MonthlyUsageRepository;
use App\Repository\TransportRepository;
use DateTimeInterface;

class TransportService
{
    protected $transportRepository;
    protected $fuelRepository;
    protected $usageRepository;

    public function __construct(
        TransportRepository $transportRepository,
        FuelReceived2019Repository $fuelRepository,
        MonthlyUsageRepository $usageRepository
    ) {
        $this->transportRepository = $transportRepository;
        $this->fuelRepository = $fuelRepository;
        $this->usageRepository = $usageRepository;
    }

    public function getAll()
    {
        return $this->transportRepository->findAll();
    }

    public function getOneAuto(int $transportId)
    {
        return $this->transportRepository->find($transportId);
    }

    public function updateOne(Transport $transport)
    {
        $this->transportRepository->save($transport);
    }

    public function getAllFuel(DateTimeInterface $currentDate)
    {
        return $this->fuelRepository->getFuelReceived($currentDate);
    }

    public function initiateTransport(array $info)
    {
        $transport = $this->transportRepository->find($info['id']);
        $transport->setFuelTankStatus($info);

        $this->transportRepository->save($transport);

        return $this;
    }
}

/*
SELECT * FROM `ware_purchased_materials`
JOIN ware_invoices on wi_id=wpm_invoice_id
where wi_data < '2019-03-31' AND wi_kontr_id=42 AND wpm_likutis>0
ORDER BY `ware_purchased_materials`.`wpm_likutis`  DESC
 */
