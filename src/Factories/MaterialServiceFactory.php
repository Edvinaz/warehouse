<?php

namespace App\Factories;

use App\Services\MaterialService;
use App\Repository\WareMaterialsRepository;

class MaterialServiceFactory
{
    private $repository;

    public function __construct(WareMaterialsRepository $repository)
    {
        $this->repository = $repository;
    }
    
    public function createMaterialServiceManager(): MaterialService
    {
        $MaterialServiceManager = new MaterialService($this->repository);

        return $MaterialServiceManager;
    }

}