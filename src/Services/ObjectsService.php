<?php
declare(strict_types=1);

namespace App\Services;

use App\Repository\BuhInvoicesRepository;
use App\Repository\WareObjectsRepository;
use App\Repository\BuhContractsRepository;
use App\Repository\BuhInvoiceContentRepository;
use App\Repository\Objects\ObjectMaterialsRepository;
use App\Repository\WareWriteOffsRepository;

class ObjectsService
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
        ObjectMaterialsRepository $warePurchasedMaterialsRepository,
        WareWriteOffsRepository $wareWriteOffsRepository
    ) {
        $this->objectsRepository = $wareObjectsRepository;
        $this->contractsRepository = $buhContractsRepository;
        $this->invoicesRepository = $buhInvoicesRepository;
        $this->invoiceContentRepository = $buhInvoiceContentRepository;
        $this->purchasedMaterialsRepository = $warePurchasedMaterialsRepository;
        $this->writeOffRepository = $wareWriteOffsRepository;
    }

    public function getObject(int $objectId)
    {
        return $this->objectsRepository->find($objectId);
    }
}
