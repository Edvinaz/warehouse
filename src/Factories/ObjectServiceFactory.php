<?php

namespace App\Factories;

use App\Services\ObjectsService;
use App\Repository\BuhInvoicesRepository;
use App\Repository\WareObjectsRepository;
use App\Repository\BuhContractsRepository;
use App\Repository\BuhInvoiceContentRepository;
use App\Repository\WarePurchasedMaterialsRepository;

class ObjectServiceFactory
{
    protected $objectsRepository;
    protected $contractsRepository;
    protected $invoicesRepository;
    protected $invoiceContentRepository;
    protected $purchasedMaterialsRepository;

    public function __construct(
        WareObjectsRepository $wareObjectsRepository,
        BuhContractsRepository $buhContractsRepository,
        BuhInvoicesRepository $buhInvoicesRepository,
        BuhInvoiceContentRepository $buhInvoiceContentRepository,
        WarePurchasedMaterialsRepository $warePurchasedMaterialsRepository
    ) {
        $this->objectsRepository = $wareObjectsRepository;
        $this->contractsRepository = $buhContractsRepository;
        $this->invoicesRepository = $buhInvoicesRepository;
        $this->invoiceContentRepository = $buhInvoiceContentRepository;
        $this->purchasedMaterialsRepository = $warePurchasedMaterialsRepository;
    }

    public function createObjectServiceManager()
    {
        $objectDetailsManager = new ObjectsService(
            $this->objectsRepository, 
            $this->contractsRepository, 
            $this->invoicesRepository,
            $this->invoiceContentRepository,
            $this->purchasedMaterialsRepository
        );

        return $objectDetailsManager;
    }

}