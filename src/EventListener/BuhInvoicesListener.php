<?php

namespace App\EventListener;

use App\Entity\Sales\BuhInvoices;
use Doctrine\ORM\Event\LifecycleEventArgs;

class BuhInvoicesListener
{
    public function postPersist(BuhInvoices $buhInvoices, LifecycleEventArgs $event)
    {
        // \var_dump('persist');
        // dd($buhInvoices);
    }

    public function postUpdate(BuhInvoices $buhInvoices, LifecycleEventArgs $event)
    {
        // dd($buhInvoices);
    }

    public function postRemove(BuhInvoices $buhInvoices, LifecycleEventArgs $event)
    {
        // dd($buhInvoices);
    }
}
