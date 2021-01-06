<?php
declare(strict_types=1);

namespace App\Models\Transport;

class FuelStatus
{
    protected $fuel;
    protected $fuelName;

    protected $fuelNorm;
    protected $distance;

    protected $beginningStatus;
    protected $receivedStatus;
    protected $usedStatus;
    protected $endingStatus;

    public function __construct(int $fuelID)
    {
        $this->setFuel($fuelID);
        $this->beginningStatus = 0.0;
        $this->receivedStatus = 0.0;
        $this->usedStatus = 0.0;
        $this->endingStatus = $this->beginningStatus + $this->receivedStatus - $this->usedStatus;
    }

    public function getBeginingStatus(): ?float
    {
        if ($this->beginningStatus) {
            return $this->beginningStatus;
        } else {
            return null;
        }
    }

    public function getReceivedStatus(): ?float
    {
        return $this->receivedStatus;
    }

    public function getUsedStatus(): ?float
    {
        return $this->usedStatus;
    }

    public function getEndingStatus(): ?float
    {
        return $this->endingStatus;
    }

    public function getFuelNorm(): float
    {
        return floatval($this->fuelNorm);
    }

    public function setFuel(int $fuel): self
    {
        $this->fuel = $fuel;

        switch ($this->fuel) {
            case 25:
                $this->fuelName = 'gasoline';
                break;
            case 26:
                $this->fuelName = 'diesel';
                break;
            case 27:
                $this->fuelName = 'gas';
                break;
        }

        return $this;
    }

    public function updateBeginingStatus(float $status)
    {
        $this->beginningStatus += $status;
        $this->endingStatus += $status;
        return $this;
    }

    public function updateReceivedStatus(float $status)
    {
        $this->receivedStatus += $status;
        $this->endingStatus += $status;
        return $this;
    }

    public function updateUsedStatus(float $status)
    {
        $this->usedStatus += $status;
        $this->endingStatus += $status * -1;
        return $this;
    }

    public function updateFuelNorm()
    {
        if ($this->distance === 0) {
            $this->fuelNorm = 0;
        } else {
            $this->fuelNorm = $this->usedStatus / ($this->distance / 100);
        }
        return $this;
    }

    public function setDistance(int $distance)
    {
        $this->distance = $distance;
        return $this;
    }

    public function toNewPeriod()
    {
        $this->beginningStatus = $this->endingStatus;
        $this->distance = 0;
        $this->receivedStatus = 0.0;
        $this->usedStatus = 0.0;
    }


    // Metodas rankiniu būdu nurodyti pabaigos likutį
    public function setEndingStatus(float $status)
    {
        $this->usedStatus = $this->beginningStatus + $this->receivedStatus - $status;
        $this->endingStatus = $status;
        return $this;
    }

    public function setFuelNorm(float $norm)
    {
        $this->fuelNorm = $norm;
        return $this;
    }

    public function getCurrentNormDistance()
    {
        if ($this->fuelNorm > 0) {
            return $this->usedStatus * 100 / $this->fuelNorm;
        } else {
            return null;
        }

    }
}
