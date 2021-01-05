<?php

namespace App\Models\Event;

use App\Entity\Purchases\WarePurchasedMaterials;
use Symfony\Contracts\EventDispatcher\Event;

class PurchasedMaterial extends Event
{
    private $id;
    private $invoice;
    private $material;
    private $quantity;
    private $price;
    private $vat;
    private $balance;
    private $object;

    public function __construct(WarePurchasedMaterials $entity)
    {
        $this->id = $entity->getId();
        $this->invoice = $entity->getInvoice();
        $this->material = $entity->getMaterial();
        $this->quantity = $entity->getQuantity();
        $this->price = $entity->getPrice();
        $this->vat = $entity->getVat();
        $this->balance = $entity->getBalance();
        $this->object = $entity->getObject();
    }

    /**
     * Get the value of id.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of invoice.
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Get the value of material.
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * Get the value of quantity.
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Get the value of price.
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Get the value of vat.
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * Get the value of balance.
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Get the value of object.
     */
    public function getObject()
    {
        return $this->object;
    }
}
