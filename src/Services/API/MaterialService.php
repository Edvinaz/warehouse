<?php

declare(strict_types=1);

namespace App\Services\API;

use App\Repository\WareMaterialsRepository;
use App\Repository\Objects\ObjectMaterialsRepository;

class MaterialService
{
    private $materialsRepository;

    public function __construct(ObjectMaterialsRepository $materialsRepository)
    {
        $this->materialsRepository = $materialsRepository;
    }

    public function getObjectReservedMaterials(int $objectId)
    {
        $materials = $this->materialsRepository->getObjectMaterials($objectId, null);

        $list  = [];
        foreach ($materials as $material) {
            $list[] = [
                $material['name'],
                $material['unit'],
                $material['quantity'],
                $material['price'],
            ];
        }
        return $list;
    }
}
