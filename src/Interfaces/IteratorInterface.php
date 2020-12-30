<?php
namespace App\Interfaces;

Interface IteratorInterface
{
    public function getNext();

    public function hasMore();
}