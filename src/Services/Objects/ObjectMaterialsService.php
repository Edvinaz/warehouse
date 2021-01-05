<?php
declare(strict_types=1);

namespace App\Services\Objects;

use App\Services\ObjectsService;

class ObjectMaterialsService extends ObjectsService
{
    public function getReservedMaterials(int $objectId)
    {
        return $this->purchasedMaterialsRepository->getObjectReservedMaterialsList($objectId, null);
    }

    public function cancelReservation(int $materialId, int $objectId)
    {
        $materials = $this->purchasedMaterialsRepository->getObjectReservedMaterial($materialId, $objectId);
        foreach ($materials as $material) {
            $material->setObject(null);
            $this->purchasedMaterialsRepository->persist($material);
        }
        $this->purchasedMaterialsRepository->save();
    }
}