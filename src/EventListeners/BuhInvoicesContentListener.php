<?php

namespace App\EventListener;

use App\Entity\Sales\BuhInvoiceContent;
use Doctrine\ORM\Event\LifecycleEventArgs;

class BuhInvoicesContentListener
{
    public function postPersist(BuhInvoiceContent $buhInvoices, LifecycleEventArgs $event)
    {
        $manager = $event->getObjectManager();
        $object = $buhInvoices->getObject();
        $events = $buhInvoices->popEvents();
        $object->updateIncome($events['balance']);
        $buhInvoices->setObject($object);
        $manager->persist($buhInvoices);
        $manager->flush();
    }

    public function postUpdate(BuhInvoiceContent $buhInvoices, LifecycleEventArgs $event)
    {
        \var_dump('update');
        dd($buhInvoices);
    }

    public function postRemove(BuhInvoiceContent $buhInvoices, LifecycleEventArgs $event)
    {
        \var_dump('remove');
        $manager = $event->getObjectManager();
        $object = $buhInvoices->getObject();
        $object->updateIncome((string) (floatval($buhInvoices->getSum()) * -1));
        // $buhInvoices->setObject($object);
        $manager->persist($object);
        $manager->flush();
    }
}
