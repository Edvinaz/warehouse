<?php
declare(strict_types=1);

namespace App\Services\Objects;

use DateTimeInterface;
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

    public function setReservedMaterials(int $objectId)
    {
        $object = $this->objectsRepository->find($objectId);
        $search = ['category' => '', 'search' => ''];
        if ($object->hasReserved()) {
            return $this->purchasedMaterialsRepository
                ->getObjectReservedMaterials($this->object, $search);

            // $this->calculateObjectReservedMaterialsByPurchaseMonth($objectId);
        }
    }

    public function calculateObjectReservedMaterialsByPurchaseMonth(int $objectId)
    {
        $list = $this->purchasedMaterialsRepository
            ->getAllObjectReservedMaterials($objectId);
        $month = [];
        foreach ($list as $item) {
            $date = $item->getInvoice()->getDate()->format('Y-m');
            if (array_key_exists($date, $month)) {
                $month[$date] += $item->getSum();
            } else {
                $month[$date] = $item->getSum();
            }
        }
        return $month;
    }

    public function getReservedMaterialsSum(int $objectId)
    {
        $sum = 0;
        foreach ($this->getReservedMaterials($objectId) as $material) {
            $sum += $material['quantity'] * $material['price'];
        }

        return $sum;
    }

    public function setMonthReservedMaterials(int $objectId, DateTimeInterface $date)
    {        
        return $this->purchasedMaterialsRepository
            ->getMonthReservedMaterials(
                $this->objectsRepository->find($objectId), 
                $date
            );
    }
}