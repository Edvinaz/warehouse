<?php
declare(strict_types=1);

namespace App\Services\Objects;

use App\Models\Worker;
use App\Services\ObjectsService;
use App\Entity\Staff\WorkerModel;
use App\Entity\Staff\ResponsibleModel;

class ObjectStaffService extends ObjectsService
{
    public function addObjectStaff(WorkerModel $worker, int $objectId)
    {
        $object = $this->findObject($objectId);
        $objectDetails = $object->getEntity();
        $objectDetails->setStaff(new Worker($worker));

        $object->setEntity($objectDetails);
        $this->objectsRepository->save($object);

        return $this;
    }

    public function addObjectManager(ResponsibleModel $worker, int $objectId)
    {
        $object = $this->findObject($objectId);
        $objectDetails = $object->getEntity();
        $objectDetails->setManager(new Worker($worker));

        $object->setEntity($objectDetails);
        $this->objectsRepository->save($object);

        return $this;
    }

    public function addObjectForemen(ResponsibleModel $worker, int $objectId)
    {
        $object = $this->findObject($objectId);
        $objectDetails = $object->getEntity();
        $objectDetails->setForemen(new Worker($worker));

        $object->setEntity($objectDetails);
        $this->objectsRepository->save($object);

        return $this;
    }

    private function findObject(int $objectId)
    {
        return $this->objectsRepository->find($objectId);
    }
}