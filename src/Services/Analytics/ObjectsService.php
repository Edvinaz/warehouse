<?php
declare(strict_types=1);

namespace App\Services\Analytics;

use App\Helpers\Analytics\ObjectModel;
use App\Services\ObjectsService AS Service;
use App\Repository\Analytics\ObjectsRepository;

class ObjectsService extends Service
{
    protected $objectsRepository;

    public function __construct(ObjectsRepository $objectsRepository)
    {
        $this->objectsRepository = $objectsRepository;
    }

    public function getAll()
    {
        $list = $this->objectsRepository->getYearObjects();
        $collection = [];
        foreach ($list as $item) {
            if ($item->getStatus() !== 'OFFER') {
                $collection[] = new ObjectModel($item);
            }
        }
        $super = null;
        return $collection;
    }

    public function getContrahentObjects(int $contrahentId)
    {
        $list = $this->objectsRepository->getContrahentYearObjects($contrahentId);
        $collection = [];
        foreach ($list as $item) {
            if ($item->getStatus() !== 'OFFER') {
                $collection[] = new ObjectModel($item);
            }
        }
        $super = null;
        return $collection;
    }
    
}