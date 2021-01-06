<?php
declare(strict_types=1);

namespace App\Models\Transport;

abstract class FuelUsage
{
    private $licensePlate;

    private $odometerStart;
    private $odometerEnd;
    private $distance;
    private $gasolineRate;

    private $gasoline;
    private $gas;
    private $diesel;

    public function setOdometerStart(int $odometer): self
    {
        $this->odometerStart = $odometer;
        return $this;
    }

    public function setOdometerEnd(int $odometer): self
    {
        $this->odometerEnd = $odometer;
        $this->distance = $odometer - $this->odometerStart;
        return $this;
    }

    public function setGasoline(FuelStatus $status): self
    {
        $this->gasoline = $status;
        return $this;
    }

    public function setGasolineRate(?float $rate)
    {
        $this->gasolineRate = $rate;
        return $this;
    }

    public function setGas(FuelStatus $status): self
    {
        $this->gas = $status;
        return $this;
    }

    public function setDiesel(FuelStatus $status): self
    {
        $this->diesel = $status;
        return $this;
    }

    public function unsetGasoline()
    {
        unset($this->gasoline);
        return $this;
    }

    public function unsetGas()
    {
        unset($this->gas);
        return $this;
    }

    public function unsetDiesel()
    {
        unset($this->diesel);
        return $this;
    }

    public function getDiesel(): ?FuelStatus
    {
        if (property_exists($this, 'diesel')) {
            return $this->diesel;
        } else {
            return NULL;
        }
    }

    public function getGasoline(): ?FuelStatus
    {
        if (property_exists($this, 'gasoline')) {
            return $this->gasoline;
        } else {
            return NULL;
        }
    }

    public function getGas(): ?FuelStatus
    {
        if (property_exists($this, 'gas')) {
            return $this->gas;
        } else {
            return NULL;
        }
    }

    public function getLicensePlate()
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $plate)
    {
        $this->licensePlate = $plate;
        return $this;
    }

    public function updateToNewPeriod()
    {
        $this->odometerStart = $this->odometerEnd;
        $this->setOdometerEnd($this->odometerEnd);
        if ($this->diesel) {
            $this->diesel->toNewPeriod();
        }
        if ($this->gasoline) {
            $this->gasoline->toNewPeriod();
        }
        if ($this->gas) {
            $this->gas->toNewPeriod();
        }

    }

    public function getOdometerStart()
    {
        return $this->odometerStart;
    }

    public function getOdometerEnd()
    {
        return $this->odometerEnd;
    }

    public function getDistance()
    {
        return null;
    }
}
