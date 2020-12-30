<?php

namespace App\EventListener;

use App\Entity\Purchases\WarePurchasedMaterials;
use App\Helpers\WarePurchasedMaterialsModifier;
use Doctrine\ORM\Event\LifecycleEventArgs;

class WarePurchasedMaterialsListener
{
    public function postPersist(WarePurchasedMaterials $entity, LifecycleEventArgs $event)
    {
        $manager = $event->getObjectManager();
        $newEntity = new WarePurchasedMaterialsModifier($entity, $manager);
        $entity = $newEntity->update();
        $manager->persist($entity);

        $manager->flush();
    }

    public function postUpdate(WarePurchasedMaterials $entity, LifecycleEventArgs $event)
    {
        $manager = $event->getObjectManager();
        $newEntity = new WarePurchasedMaterialsModifier($entity, $manager);
        $entity = $newEntity->update();
        $manager->persist($entity);

        $manager->flush();
    }

    public function preRemove(WarePurchasedMaterials $entity, LifecycleEventArgs $event)
    {
        $manager = $event->getObjectManager();
        $newEntity = new WarePurchasedMaterialsModifier($entity, $manager);
        $invoice = $newEntity->getUpdatedInvoice();
        if (!\is_null($entity->getObject())) {
            $object = $newEntity->getUpdatedObject();
            $manager->persist($object);
        }

        if (!is_null($entity->getNote())) {
            $newEntity->updateNoteRemove();
        }
        $manager->persist($invoice);

        $manager->flush();
    }
}
