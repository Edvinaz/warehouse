<?php

namespace App\Helpers;

use App\Entity\Transport\Transport;
use App\Entity\Purchases\WareInvoices;
use App\Entity\Objects\WareObjects;
use App\Entity\Purchases\WarePurchasedMaterials;
use Doctrine\ORM\EntityManagerInterface;

class WarePurchasedMaterialsModifier
{
    protected $manager;
    private $purchasedMaterials;
    private $difference;

    public function __construct(WarePurchasedMaterials $entity, EntityManagerInterface $manager)
    {
        $this->purchasedMaterials = $entity;
        $this->manager = $manager;

        $this->setDifference();
    }

    public function update()
    {
        $this->updateInvoice();

        return $this->purchasedMaterials;
    }

    public function updateInvoice()
    {
        $invoice = $this->purchasedMaterials->getInvoice();
        $invoice->updateAmount($this->difference);
        $invoice->updateVAT($this->setDifferenceVAT());
        $this->purchasedMaterials->setInvoice($invoice);

        return $this;
    }

    public function updateObject()
    {
        $object = $this->purchasedMaterials->getObject();
        if (1 === $this->purchasedMaterials->getType()) {
            $object->updateReservedMaterials($this->difference);
        } elseif (20 === $this->purchasedMaterials->getType()) {
            $object->updateServices($this->difference);
        }
        $this->purchasedMaterials->setObject($object);

        return $this;
    }

    public function setDifference()
    {
        $events = $this->purchasedMaterials->popEvents();

        if (\array_key_exists('price', $events)) {
            $price1 = $events['price'][0];
            $price2 = $events['price'][1];
        } else {
            $price1 = $this->purchasedMaterials->getPrice();
            $price2 = $this->purchasedMaterials->getPrice();
        }
        if (\array_key_exists('quantity', $events)) {
            $quantity1 = $events['quantity'][0];
            $quantity2 = $events['quantity'][1];
        } else {
            $quantity1 = $this->purchasedMaterials->getQuantity();
            $quantity2 = $this->purchasedMaterials->getQuantity();
        }
        $this->difference = ($price2 * $quantity2) - ($price1 * $quantity1);

        if (\array_key_exists('object', $events)) {
            $this->updateObjects($events['object']);
        } elseif (!\is_null($this->purchasedMaterials->getObject())) {
            $this->updateObject();
        }

        if (\array_key_exists('balance decrease', $events)) {
            // dd($events);
        }

        if (\array_key_exists('note', $events)) {
            $this->updateNote($events['note']);
        }

        return $this;
    }

    public function setDifferenceVAT()
    {
        return $this->difference * ($this->purchasedMaterials->getVat() / 100);
    }

    /**
     * Updating invoice balance after material removal.
     */
    public function getUpdatedInvoice(): WareInvoices
    {
        $invoice = $this->purchasedMaterials->getInvoice();
        $invoice->updateAmount(-1 * $this->purchasedMaterials->getSum());
        $invoice->updateVAT(-1 * $this->purchasedMaterials->getVatSum());

        return $invoice;
    }

    /**
     * Updating object after material removal.
     */
    public function getUpdatedObject()
    {
        $object = $this->purchasedMaterials->getObject();
        if (1 === $this->purchasedMaterials->getType()) {
            $object->updateReservedMaterials(-1 * $this->purchasedMaterials->getSum());
        } elseif (20 === $this->purchasedMaterials->getType()) {
            $object->updateServices(-1 * $this->purchasedMaterials->getSum());
        }

        return $object;
    }

    /**
     * Update transport Cost after remove purchase.
     */
    public function updateNoteRemove()
    {
        $transport = $this->manager->getRepository(Transport::class)->findAll();

        for ($i = 0; $i < count($transport); ++$i) {
            if (
                (string) $transport[$i] === $this->purchasedMaterials->getNote()
            ) {
                $cost = $transport[$i]->getCosts($this->purchasedMaterials->getDate()->format('Y-m'));
                $cost->deleteCosts($this->purchasedMaterials);

                break;
            }
        }

        return true;
    }

    /**
     * Update Objects reserved materials.
     */
    private function updateObjects(?WareObjects $wareObjects)
    {
        if (!\is_null($wareObjects)) {
            $wareObjects->updateReservedMaterials(-1 * $this->purchasedMaterials->getSum());
        }
        $object = $this->purchasedMaterials->getObject();
        if (!is_null($object)) {
            if (1 === $this->purchasedMaterials->getType()) {
                $object->updateReservedMaterials($this->purchasedMaterials->getSum());
            } elseif (20 === $this->purchasedMaterials->getType()) {
                $object->updateServices($this->purchasedMaterials->getSum());
            }
            $this->purchasedMaterials->setObject($object);
        }
        
    }

    /**
     * Dealing with notes. Add Transport info in purchase notes.
     */
    private function updateNote(array $note)
    {
        $transport = $this->manager->getRepository(Transport::class)->findAll();
        for ($i = 0; $i < count($transport); ++$i) {
            if (
                (string) $transport[$i] === $note[1]
            ) {
                $cost = $transport[$i]->getCosts($this->purchasedMaterials->getDate()->format('Y-m'));

                $cost->updateCosts($this->purchasedMaterials);

                $transport[$i]->updateCosts($cost, $this->purchasedMaterials->getDate()->format('Y-m'));

                break;
            }
        }

        return true;
    }
}
