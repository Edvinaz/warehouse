<?php
declare(strict_types=1);

namespace App\Services;

use App\Repository\BuhInvoicesRepository;
use App\Repository\WareObjectsRepository;
use App\Repository\BuhContractsRepository;
use App\Repository\BuhInvoiceContentRepository;
use App\Repository\WarePurchasedMaterialsRepository;

class ObjectsService
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
        $this->invoicesContentRepository = $buhInvoiceContentRepository;
        $this->purchasedMaterialsRepository = $warePurchasedMaterialsRepository;
    }
}
