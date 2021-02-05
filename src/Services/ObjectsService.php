<?php
declare(strict_types=1);

namespace App\Services;

use App\Entity\Objects\WareObjects;
use App\Repository\BuhInvoicesRepository;
use App\Repository\WareObjectsRepository;
use App\Entity\Objects\InstallationObject;
use App\Repository\BuhContractsRepository;
use App\Repository\WareWriteOffsRepository;
use App\Repository\BuhInvoiceContentRepository;
use App\Repository\Objects\ObjectMaterialsRepository;

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

    public function getObject(
        int $objectId
    ): WareObjects {
        if ($objectId === 0) {
            return new InstallationObject();
        }
        return $this->objectsRepository->find($objectId);
    }
}
