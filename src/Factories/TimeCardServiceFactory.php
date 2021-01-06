<?php

namespace App\Factories;

use App\Services\TimeCard\TimeCardService;
use App\Repository\TimeCardRepository;
use App\Repository\StaffRepository;

class TimeCardServiceFactory
{
    private $repository;
    private $staffRepository;

    public function __construct(TimeCardRepository $repository, StaffRepository $staffRepository)
    {
        $this->repository = $repository;
        $this->staffRepository = $staffRepository;
    }
    
    public function createTimeCardServiceManager(): TimeCardService
    {
        $timeCardServiceManager = new TimeCardService($this->repository, $this->staffRepository);

        return $timeCardServiceManager;
    }

}