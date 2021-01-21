<?php
declare(strict_types=1);

namespace App\Services\Objects;

use App\Services\ObjectsService;
use App\Entity\Sales\BuhContracts;

class ObjectContractService extends ObjectsService
{
    public function getContract(
        int $objectId
    ): BuhContracts {
        $object = $this->getObject($objectId);
        if ($object->getBuhContracts()) {
            return $object->getBuhContracts();
        }
        $contract = new BuhContracts();
        $contract->setContrahent($object->getContrahent());
        $contract->setObject($object);

        return $contract;
    }

    public function saveContract(
        BuhContracts $contract
    ): void {
        $this->contractsRepository->save($contract);
    }
}
