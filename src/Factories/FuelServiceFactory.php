<?php

declare(strict_types=1);

namespace App\Factories;

use App\Repository\Fuel\FuelRepository;
use App\Services\FuelService;

class FuelServiceFactory
{
    private $repository;

    public function __construct(
        FuelRepository $repository
    ) {
        $this->repository = $repository;
    }

    public function createFuelServiceManager(): FuelService
    {
        return new FuelService(
            $this->repository
        );
    }
}
