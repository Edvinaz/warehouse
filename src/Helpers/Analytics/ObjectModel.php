<?php

declare(strict_types=1);

namespace App\Helpers\Analytics;

use App\Entity\Objects\WareObjects;

class ObjectModel
{
    private $object;
    private $objectId;
    private $contrahent;
    private $contrahentId;
    private $name;
    private $reserved;
    private $income;
    private $services;
    private $debited;
    private $workedHours;

    private $hourPrice = 4.44;

    public function __construct(WareObjects $object)
    {
        $this->object = $object;
        $this->splash();
    }

    public function getContrahent(): string
    {
        return $this->contrahent;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReservedMaterials(): ?string
    {
        return $this->reserved;
    }

    public function getIncome(): ?string
    {
        return $this->income;
    }

    public function getServices(): ?string
    {
        return $this->services;
    }

    public function getDebited(): ?string
    {
        return $this->debited;
    }

    public function getWorkedHours(): ?string
    {
        return (string) ($this->workedHours * $this->hourPrice);
    }

    public function getProfit(): ?string
    {
        // dump($this->reserved);
        return (string) ($this->income - $this->services - $this->debited - ($this->workedHours * $this->hourPrice));
    }

    public function getAlert(): string
    {
        if ($this->getProfit() > 0) {
            return 'table-success';
        } else if ($this->getProfit() < 0) {
            return 'table-danger';
        } else if($this->getProfit() == 0 && $this->getReservedMaterials() > 0) {
            return 'table-warning';
        }
        return 'table-default';
    }

    public function getId()
    {
        return $this->contrahentId;
    }

    public function getObjectId()
    {
        return $this->object->getId();
    }

    private function splash()
    {
        $this->contrahent = $this->object->getContrahent()->getName();
        $this->contrahentId = $this->object->getContrahent()->getId();
        $this->name = $this->object->getNumber().' '.$this->object->getName().', '.$this->object->getAdress();
        $this->reserved = $this->object->getReserved();
        $this->income = $this->object->getIncome();
        $this->services = $this->object->getServices();
        $this->debited = $this->object->getDebitedMaterials();
        $this->workedHours = $this->object->getWorkedHours();

        return $this;
    }
}
