<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\DebitMaterialHelper;
use App\Repository\WareDebitedMaterialsRepository;
use App\Repository\WarePurchasedMaterialsRepository;
use App\Repository\WareWriteOffsRepository;
use App\Services\DebitMaterialsService;
use Symfony\Component\HttpFoundation\Session\Session;

class WriteOffModel
{
    protected $writeOffRepository;
    protected $debitedMaterialsRepository;
    // protected $debitMaterialService;
    protected $purchasedMaterialsRepository;

    protected $entity;
    protected $materials;

    protected $warehouseMaterials;

    protected $debitMaterialHelper;

    public function __construct(
        WareWriteOffsRepository $writeOffsRepository,
        WareDebitedMaterialsRepository $debitedMaterialsRepository,
        WarePurchasedMaterialsRepository $purchasedMaterialsRepository,
        DebitMaterialHelper $debitMaterialHelper,
        DebitMaterialsService $debitMaterialService
    ) {
        $this->writeOffRepository = $writeOffsRepository;
        $this->debitedMaterialsRepository = $debitedMaterialsRepository;
        $this->purchasedMaterialsRepository = $purchasedMaterialsRepository;
        $this->debitMaterialHelper = $debitMaterialHelper;
        $this->debitMaterialService = $debitMaterialService;
    }

    public function initiate(int $entityId, ?array $search)
    {
        $this->entity = $this->writeOffRepository->find($entityId);
        $this->materials = $this->debitedMaterialsRepository->getWriteOffMaterialList($entityId);

        if (is_null($search)) {
            $search = ['search' => '', 'category' => ''];
        }
        
        $this->setWarehouseMaterials($search);
    }

    public function newDebitMaterial(int $materialID, bool $isMaterialDebiting)
    {
        $this->debitMaterialHelper = $this->debitMaterialHelper->createNew($materialID, $this->entity, $isMaterialDebiting);

        return $this->debitMaterialHelper;
    }

    public function debitSelectedMaterial(DebitMaterialHelper $debitMaterial): void
    {
        dump('debitSelectedMaterial');
        $this->debitMaterialService->debitMaterial($debitMaterial);
    }

    public function unDebitSelectedMaterial(DebitMaterialHelper $debitMaterial): void
    {
        $this->debitMaterialService->unDebitMaterial($debitMaterial);
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function getMaterials()
    {
        return $this->materials;
    }

    public function getWarehouseMaterials()
    {

        return $this->warehouseMaterials;
    }

    protected function setWarehouseMaterials(?array $search)
    {
        $session = new Session();
        if ($this->entity->getObject() && $this->entity->getObject()->hasReserved() && $session->get('reserved')) {
            $this->warehouseMaterials = $this->purchasedMaterialsRepository->getObjectReservedMaterials($this->entity->getObject(), $search);
            $session->set('reserved', true);
        } else {
            $this->warehouseMaterials = $this->purchasedMaterialsRepository->getNotReservedMaterials($search);
            $session->set('reserved', false);
        }
    }
}
