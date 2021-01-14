<?php

namespace App\Helpers\Analytics;

class PurchaseContrahentModel
{
    protected $name;
    protected $id;

    protected $amount;
    protected $materials;

    protected $workMaterials;
    protected $fuel;
    protected $stationerySupplies;
    protected $transportService;
    protected $workClothesPPE;
    protected $otherServices;

    protected $other;
    

    /**
     * Get the value of amount
     */ 
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the value of amount
     *
     * @return  self
     */ 
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of materials
     */ 
    public function getMaterials()
    {
        return $this->materials;
    }

    /**
     * Set the value of materials
     *
     * @return  self
     */ 
    public function setMaterials(array $materials)
    {
        foreach ($materials as $material) {
            $sum = $material->getPrice() * $material->getQuantity();
            switch($material->getType()) {
                case 1:
                    $this->addWorkMaterials($sum);
                break;
                case 2:
                    $this->addFuel($sum);
                break;
                case 10:
                    $this->addStationerySupplies($sum);
                break;
                case 11:
                    $this->addTransportServices($sum);
                break;
                case 12:
                    $this->addWorkClothesPPE($sum);
                break;
                case 20:
                    $this->addOtherServices($sum);
                break;
                default:
                break;
            }
        }
        $this->materials = $materials;

        return $this;
    }

    private function addWorkMaterials(float $sum)
    {
        if (is_null($this->workMaterials)) {
            $this->workMaterials = 0;
        }
        $this->workMaterials += $sum;

        return $this;
    }
    private function addFuel(float $sum)
    {
        if (is_null($this->fuel)) {
            $this->fuel = 0;
        }
        $this->fuel += $sum;

        return $this;
    }
    private function addStationerySupplies(float $sum)
    {
        if (is_null($this->stationerySupplies)) {
            $this->stationerySupplies = 0;
        }
        $this->stationerySupplies += $sum;

        return $this;
    }
    private function addTransportServices(float $sum)
    {
        if (is_null($this->transportService)) {
            $this->transportService = 0;
        }
        $this->transportService += $sum;

        return $this;
    }
    private function addWorkClothesPPE(float $sum)
    {
        if (is_null($this->workClothesPPE)) {
            $this->workClothesPPE = 0;
        }
        $this->workClothesPPE += $sum;

        return $this;
    }
    private function addOtherServices(float $sum)
    {
        if (is_null($this->otherServices)) {
            $this->otherServices = 0;
        }
        $this->otherServices += $sum;

        return $this;
    }

    /**
     * Get the value of workMaterials
     */ 
    public function getWorkMaterials()
    {
        return $this->workMaterials;
    }

    /**
     * Get the value of fuel
     */ 
    public function getFuel()
    {
        return $this->fuel;
    }

    public function getStationerySupplies()
    {
        return $this->stationerySupplies;
    }
    
    public function getTransportService()
    {
        return $this->transportService;
    }
    
    public function getWorkClothesPPE()
    {
        return $this->workClothesPPE;
    }
    
    public function getOtherServices()
    {
        return $this->otherServices;
    }

    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}