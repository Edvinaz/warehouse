<?php

namespace App\EventListener;

use App\Entity\Debition\WareDebitedMaterials;
use App\Entity\Purchases\WareInvoices;
use App\Entity\Purchases\WarePurchasedMaterials;
use App\Helpers\WarePurchasedMaterialsModifier;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PurchaseEventSubscriber implements EventSubscriber
{
    private $events = [];
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcherInterface)
    {
        $this->eventDispatcher = $eventDispatcherInterface;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
            Events::preUpdate,
        ];
    }

    public function preUpdate(PreUpdateEventArgs $event)
    {
        // $this->doCollect($event);
        // dd($event->getNewValue('price'));
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        $this->doCollect($event);
        // $this->index($args);
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $this->doCollect($event);
        // $this->index($args);
    }

    public function postRemove(LifecycleEventArgs $event)
    {
        $this->doCollect($event);
        // \var_dump('removed'.$args);
    }

    public function index(LifecycleEventArgs $args)
    {
        // $entity = $args->getObject();
        // dd($entity);

        // if ($entity instanceof WarePurchasedMaterials) {
        //     $manager = $args->getObjectManager();
        //     $newEntity = new WarePurchasedMaterialsModifier($entity);
        //     $entity = $newEntity->update();
        //     // $entity->setInvoice($newEntity->updateInvoice($entity->getInvoice()));
        //     // $manager->flush();
        //     // $manager->persist($entity);
        // }

        // if ($entity instanceof WareDebitedMaterials) {
        //     $manager = $args->getObjectManager();
        //     $e2 = $manager->getRepository(WareDebitedMaterials::class)->find($entity->getId());
        //     \var_dump('Debited materials '.$entity->getAmount().' - '.$e2->getAmount());
        //     // \var_dump($entity);
        // }

        // if ($entity instanceof WareInvoices) {
        //     // dd('save invoice');
        // }
    }

    /**
     * Returns all collected events and then clear those.
     */
    public function dispatchCollectedEvents(): void
    {
        // $events = $this->events;
        // $this->events = [];

        // foreach ($events as $event) {
        //     // Here we use Symfony < 4.3 syntax:
        //     $this->eventDispatcher->dispatch(get_class($event), $event);
        //     // Otherwise you can do just this:
        //     //$this->eventDispatcher->dispatch($event);
        // }

        // // Maybe listeners emitted some new events!
        // if ($this->events) {
        //     $this->dispatchCollectedEvents();
        // }
    }

    /**
     * Optional, we will see why it can be useful later.
     */
    public function hasUndispatchedEvents(): bool
    {
        return 0 !== count($this->events);
    }

    private function doCollect(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        // dd($entity);
        // if (!$entity instanceof RaiseEventsInterface) {
        //     return;
        // }

        // foreach ($entity->popEvents() as $event) {
            // We index by object hash, not to have the same event twice
            // dd($event);
            // $this->events[spl_object_hash($event)] = $event;
        // }
    }
}
