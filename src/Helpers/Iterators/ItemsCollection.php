<?php
namespace App\Helpers\Iterators;

use Iterator;
use IteratorAggregate;

class ItemsCollection implements IteratorAggregate
{
    private $items = [];

    public function getItems()
    {
        return $this->items;
    }

    public function addItem($item)
    {
        $this->items[] = $item;
    }

    public function getIterator(): Iterator
    {
        return new ItemsIterator($this);
    }
}