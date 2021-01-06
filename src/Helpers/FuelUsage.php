<?php
declare(strict_types=1);

namespace App\Helpers;

use Error;

class FuelUsage
{
    protected $fuelTankBalance;

    protected $receivedFuel;

    protected $usedFuel;

    protected $fuelNorm;

    public function __construct(float $balance)
    {
        $this->setFuelTankBalance($balance);     
        $this->receivedFuel = [];   
    }

    /**
     * set fuel tank balance
     *
     * @param integer $balance
     * @return self
     */
    public function setFuelTankBalance(float $balance): self
    {
        $this->fuelTankBalance = $balance;

        return $this;
    }

    /**
     * add received fuel to array with purchase date and time as $key and fuel amount as $balance (must be numeric)
     *
     * @param string $key
     * @param string $balance
     * @return self
     */
    public function setReceivedFuel(string $key, string $balance): self
    {
        if (is_null($this->receivedFuel)){
            $this->receivedFuel = [];
        }
        foreach ($this->receivedFuel as $fuel => $value) {
            if (explode(' ', $fuel)[1] === '00:00') {
                unset($this->receivedFuel[$fuel]);
            }
        }
        if (is_numeric($balance)) {
            $this->receivedFuel[$key] = $balance;
        } else {
            throw new Error('balance must be numeric');
        }

        return $this;
    }

    /**
     * removes received fuel with given key
     *
     * @param string $key
     * @return self
     */
    public function removeReceivedFuel(string $key): self
    {
        if ($this->enoughFuel($key)) {
            unset($this->receivedFuel[$key]);
        } else {
            throw new Error('Not enough fuel');
        }

        return $this;
    }

    /**
     * checks is there enough fuel to remove received fuel amount
     *
     * @param string $key
     * @return boolean
     */
    private function enoughFuel(string $key): bool
    {
        $balance = array_sum($this->receivedFuel) - $this->receivedFuel[$key];

        if ($balance >= 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * calculates received fuel amount
     *
     * @return float
     */
    public function checkReceivedFuel(): float
    {
        if (is_null($this->receivedFuel)) {
            $this->receivedFuel = [];
        }
        return array_sum($this->receivedFuel);
    }

    public function getFuelBalance(): float
    {
        if (is_null($this->receivedFuel)) {
            $this->receivedFuel = [];
        }
        $balance = $this->fuelTankBalance + array_sum($this->receivedFuel) - $this->usedFuel;

        return  $balance;
    }

    public function getFuelTank(): float
    {
        return $this->fuelTankBalance;
    }

    public function getUsedFuel(): float
    {
        if (is_null($this->usedFuel)) {
            return 0;
        }
        return $this->usedFuel;
    }

    public function getFuelNorm(): float
    {
        if (is_null($this->fuelNorm)) {
            return 0;
        }
        return $this->fuelNorm;
    }

    public function setUsedFuel(float $usedFuel):self
    {
        $this->usedFuel = $usedFuel;

        return $this;
    }

    public function setFuelNorm(float $fuelNorm)
    {
        $this->fuelNorm = $fuelNorm;

        return $this;
    }

    /**
     * get only received fuel without usage
     *
     * @return float
     */
    public function getReceivedFuel(): float
    {
        if (is_null($this->receivedFuel)) {
            $this->receivedFuel = [];
        }
        $balance = $this->fuelTankBalance + array_sum($this->receivedFuel);

        return  $balance;
    }
}
