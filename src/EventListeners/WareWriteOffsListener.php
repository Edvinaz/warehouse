<?php

namespace App\EventListener;

use App\Entity\Debition\WareWriteOffs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Exception;

class WareWriteOffsListener
{
    public function postPersist(WareWriteOffs $wareWriteOffs, LifecycleEventArgs $event)
    {
        \var_dump('persist');
        // dd($wareWriteOffs);
    }

    public function postUpdate(WareWriteOffs $wareWriteOffs, LifecycleEventArgs $event)
    {
        \var_dump('update');
        // dd($wareWriteOffs);
    }

    public function preRemove(WareWriteOffs $wareWriteOffs, LifecycleEventArgs $event)
    {
        if (count($wareWriteOffs->getWareDebitedMaterials()) > 0) {
            throw new Exception('can\'t delete');
        }
    }
}
