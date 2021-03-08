<?php
declare(strict_types=1);

namespace App\Services\API;

use App\Repository\WareObjectsRepository;

class ObjectsService
{
    private $objectRepository;

    public function __construct(WareObjectsRepository $wareObjectsRepository)
    {
        $this->objectRepository = $wareObjectsRepository;
    }

    public function getObject(int $objectNumber)
    {
        $object = $this->objectRepository->findOneBy(['number' => $objectNumber]);
        return [
            'id' => $object->getId(),
            'number' => (string) $object->getNumber(),
            'object' => $object->getName(),
            'address' => $object->getAdress(),
            'client' => $object->getContrahent()->getName()
        ];
    }
}