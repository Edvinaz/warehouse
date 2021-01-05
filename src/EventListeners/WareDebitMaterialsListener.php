<?php

namespace App\EventListener;

use App\Entity\Debition\WareDebitedMaterials;
use App\Helpers\WareDebitedMaterialsModifier;
use Doctrine\ORM\Event\LifecycleEventArgs;

class WareDebitMaterialsListener
{
    public function postPersist(WareDebitedMaterials $wareDebitedMaterials, LifecycleEventArgs $event)
    {
        $manager = $event->getObjectManager();
        $modifier = new WareDebitedMaterialsModifier($wareDebitedMaterials);
        $debited = $modifier->persist();
        $manager->persist($debited);
        $manager->flush();
    }

    public function postUpdate(WareDebitedMaterials $wareDebitedMaterials, LifecycleEventArgs $event)
    {
        $manager = $event->getObjectManager();
        $modifier = new WareDebitedMaterialsModifier($wareDebitedMaterials);
        $manager->persist($modifier->update());
        $manager->flush();
    }

    public function postRemove(WareDebitedMaterials $wareDebitedMaterials, LifecycleEventArgs $event)
    {
        $manager = $event->getObjectManager();
        $modifier = new WareDebitedMaterialsModifier($wareDebitedMaterials);
        $modify = $modifier->remove();
        $manager->persist($modify->getWriteoff());
        $manager->persist($modify->getPurchase());
        $manager->flush();
    }
}
