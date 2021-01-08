<?php
declare(strict_types=1);

namespace App\Traits;

use App\Helpers\Iterators\ItemsCollection;
use App\Settings\Settings;
use Iterator;

trait IteratorTrait
{
    private function iterate(int $page): Iterator
    {
        $list = new ItemsCollection();
        $chunk = [];

        foreach ($this->list as $item) {

            $chunk[] = $item;
            
            if (count($chunk) === Settings::ITEMS) {
                $list->addItem($chunk);
                $chunk = [];
            }
        }
        if (count($chunk) > 0 || count($list->getItems()) < 1) {
            $list->addItem($chunk);
        }

        $iterator = $list->getIterator();
        for ($i=0; $i<$page; $i++) {
            $iterator->next();
        }
        return $iterator;
    }
}
