<?php
declare(strict_types=1);

namespace App\Services\Objects;

use App\Settings\Settings;
use App\Services\ObjectsService;
use App\Helpers\Iterators\ItemsCollection;
use App\Traits\IteratorTrait;

class ObjectListService extends ObjectsService
{
    use IteratorTrait;

    private $list;

    public function getObjectList(
        string $search = '', 
        string $objectStatus, 
        int $page = 0
    ) {
        // TODO implement objectStatus
        $this->setObjectList();

        $list = new ItemsCollection();

        if (empty($search)) {
            return $this->iterate($page);
        }

        $chunk = [];

        foreach ($this->list as $item) {
            if (false !== strpos(strtolower($item->getContrahent()->getName()), strtolower($search))) {
                $chunk[] = $item;
            } else if (false !== strpos(strtolower($item->getAdress()), strtolower($search))) {
                $chunk[] = $item;
            } else if (false !== strpos(strtolower($item->getName()), strtolower($search))) {
                $chunk[] = $item;
            }
            if (count($chunk) === Settings::ITEMS) {
                $list->addItem($chunk);
                $chunk = [];
            }
        }
        if (count($chunk) > 0) {
            $list->addItem($chunk);
        }

        return $list->getIterator();
    }

    private function setObjectList(): self
    {
        $this->list = $this->objectsRepository->findAll();

        return $this;
    }
}
