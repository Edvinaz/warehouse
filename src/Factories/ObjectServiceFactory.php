<?php

namespace App\Factories;

use App\Services\ObjectsService;
use App\Repository\BuhInvoicesRepository;
use App\Repository\WareObjectsRepository;
use App\Repository\BuhContractsRepository;
use App\Repository\BuhInvoiceContentRepository;
use App\Repository\WarePurchasedMaterialsRepository;
use App\Repository\WareWriteOffsRepository;

class ObjectServiceFactory
{
    protected $objectsRepository;
    protected $contractsRepository;
    protected $invoicesRepository;
    protected $invoiceContentRepository;
    protected $purchasedMaterialsRepository;
    protected $writeOffRepository;

    public function __construct(
        WareObjectsRepository $wareObjectsRepository,
        BuhContractsRepository $buhContractsRepository,
        BuhInvoicesRepository $buhInvoicesRepository,
        BuhInvoiceContentRepository $buhInvoiceContentRepository,
        WarePurchasedMaterialsRepository $warePurchasedMaterialsRepository,
        WareWriteOffsRepository $wareWriteOffsRepository
    ) {
        $this->objectsRepository = $wareObjectsRepository;
        $this->contractsRepository = $buhContractsRepository;
        $this->invoicesRepository = $buhInvoicesRepository;
        $this->invoiceContentRepository = $buhInvoiceContentRepository;
        $this->purchasedMaterialsRepository = $warePurchasedMaterialsRepository;
        $this->writeOffRepository = $wareWriteOffsRepository;
    }

    public function createObjectServiceManager()
    {
        $objectDetailsManager = new ObjectsService(
            $this->objectsRepository, 
            $this->contractsRepository, 
            $this->invoicesRepository,
            $this->invoiceContentRepository,
            $this->purchasedMaterialsRepository,
            $this->writeOffRepository
        );

        return $objectDetailsManager;
    }

}