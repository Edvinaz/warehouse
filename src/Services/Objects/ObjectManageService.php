<?php
declare(strict_types=1);

namespace App\Services\Objects;

use App\Models\Worker;
use App\Models\ObjectDetails;
use App\Services\ObjectsService;
use App\Entity\Staff\WorkerModel;
use App\Entity\Objects\WareObjects;
use App\Entity\Staff\ResponsibleModel;

class ObjectManageService extends ObjectsService
{
    public function saveObject(WareObjects $object): self
    {
        if (is_null($object->getEntity())) {
            $details = new ObjectDetails();
        } else {
            $details = $object->getEntity();
        }

        $object->setEntity($details);
        $this->objectsRepository->save($object);

        return $this;
    }

    public function deleteObject(WareObjects $object): void
    {
        //TODO throw new \Exception('Object won\'t be deleted');
        
        $this->objectsRepository->deleteObject($object);
    }

    public function calculateObjectReservedMaterialsByPurchaseMonth(
        int $objectId
    ) {
        $list = $this->purchasedMaterialsRepository->getAllObjectReservedMaterials($objectId);
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

    public function updateStatus(int $objectId, string $objectStatus)
    {
        $object = $this->getObject($objectId);
        $object->setStatus($objectStatus);
        $this->objectsRepository->save($object);

        return $this;
    }
}
