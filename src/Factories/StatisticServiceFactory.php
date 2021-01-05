<?php

namespace App\Factories;

use App\Services\StatisticService;
use Doctrine\ORM\EntityManagerInterface;

class StatisticServiceFactory
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
    }

    public function createStatisticServiceManager(): StatisticService
    {
        return new StatisticService(
            $this->entityManager
        );
    }
}
