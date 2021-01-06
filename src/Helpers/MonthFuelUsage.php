<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entity\Transport\MonthlyUsage;
use App\Entity\Transport\Transport;
use DateTime;
use Error;

class MonthFuelUsage
{
    protected $period;

    protected $mainFuel;

    protected $secondaryFuel;

    protected $tachometerStart;

    protected $tachometerEnd;

    protected $distance;

    /**
     * Default secondary fuel index
     * TODO: set default fuel index for each of the transport
     *
     * @var float
     */
    protected $secondaryFuelIndex = 1.2;

    public function __construct(Transport $transport, ?MonthlyUsage $usage)
    {
        if (is_null($usage)) {
            $this->tachometerStart = $transport->getClass()->getTachometerPurchased();
            $this->tachometerEnd = $transport->getClass()->getTachometerPurchased();
            $this->distance = 0;
            $this->mainFuel = new FuelUsage(10);
            if (!is_null($transport->getSecondaryFuel())) {
                $this->secondaryFuel = new FuelUsage(10);
            }
        } else {
            $this->tachometerStart = $usage->getClass()->getTachometerEnd();
            $this->tachometerEnd = $usage->getClass()->getTachometerEnd();
            $this->distance = 0;
            $this->mainFuel = new FuelUsage($usage->getClass()->getMainFuelBalance());
            if (!is_null($transport->getSecondaryFuel())) {
                $this->secondaryFuel = new FuelUsage($usage->getClass()->getSecondaryFuelBalance());
            }
        }
    }

    /**
     * set class fuel usage month
     *
     * @param DateTime $date
     * @return self
     */
    public function setPeriod(DateTime $date): self
    {
        $this->period = $date;

        return $this;
    }

    /**
     * returns tachometer end case for new period
     *
     * @return integer
     */
    public function getTachometerEnd(): int
    {
        return (int) $this->tachometerEnd;
    }

    public function getMainFuelBalance()
    {
        return $this->mainFuel->getFuelBalance();
    }

    public function getSecondaryFuelBalance()
    {
        if (is_null($this->secondaryFuel)) {
            return null;
        } else {
            return $this->secondaryFuel->getFuelBalance();
        }
    }

    public function setReceivedMainFuel(array $fuel, string $quantity): self
    {
        $this->mainFuel->setReceivedFuel($fuel[2], $quantity);
        return $this;
    }

    public function setReceivedSecondaryFuel(array $fuel, string $quantity): self
    {
        $this->secondaryFuel->setReceivedFuel($fuel[2], $quantity);
        return $this;
    }

    public function unsetReceivedMainFuel(string $key)
    {
        $this->mainFuel->removeReceivedFuel($key);
        return $this;
    }

    public function unsetReceivedSecondaryFuel(string $key)
    {
        $this->secondaryFuel->removeReceivedFuel($key);
        return $this;
    }

    public function setTachometerEnd(string $tachometer)
    {
        $this->tachometerEnd = $tachometer;
        $this->setDistance();
        return $this;
    }

    private function setDistance()
    {
        $this->distance = $this->tachometerEnd - $this->tachometerStart;
        return $this;
    }

    public function getTachometerStart()
    {
        return $this->tachometerStart;
    }

    public function getDistance()
    {
        return $this->distance;
    }

    public function getMonthMainFuel(): float
    {
        return $this->mainFuel->getFuelBalance();
    }

    public function getMonthSecondaryFuel(): ?float
    {
        if(is_null($this->secondaryFuel)) {
            return null;
        }

        return $this->secondaryFuel->getFuelBalance();
    }

    public function getMainFuel(): FuelUsage
    {
        return $this->mainFuel;
    }

    public function getSecondaryFuel(): ?FuelUsage
    {
        return $this->secondaryFuel;
    }

    public function calculateUsage(float $mainFuelRemainder, ?float $secondaryFuelRemainder): self
    {
        if ($this->mainFuel->getReceivedFuel() - $mainFuelRemainder < 0) {
            throw new Error('Not enough main fuel');
        } else if (!is_null($this->secondaryFuel) && ($this->secondaryFuel->getReceivedFuel() - $secondaryFuelRemainder < 0)) {
            throw new Error('Not enough secondary fuel');
        }

        $mainUsage = $this->mainFuel->getReceivedFuel() - $mainFuelRemainder;
        $this->mainFuel->setUsedFuel($mainUsage);

        if (!is_null($this->secondaryFuel)) {
            $secondaryUsage = $this->secondaryFuel->getReceivedFuel() - $secondaryFuelRemainder;
            $this->secondaryFuel->setUsedFuel($secondaryUsage);
        } else {
            $secondaryUsage = 0;
        }

        $fuelNorm = (($this->secondaryFuelIndex * $mainUsage + $secondaryUsage) * 100) / ($this->secondaryFuelIndex * $this->getDistance());  

        $this->mainFuel->setFuelNorm($fuelNorm);

        if (!is_null($this->secondaryFuel)) {
            $this->secondaryFuel->setFuelNorm($fuelNorm * $this->secondaryFuelIndex);
        }
        
        return $this;
    }

    public function getPeriodEndSummary(): array
    {
        return [$this->getTachometerEnd(), $this->getMainFuelBalance(), $this->getSecondaryFuelBalance()];
    }

    public function updateMonthBegin(array $update)
    {
        $this->tachometerStart = $update[0];
        $this->mainFuel->setFuelTankBalance($update[1]);
        if (!is_null($update[2])) {
            $this->secondaryFuel->setFuelTankBalance($update[2]);
        }
        return $this;
    }
}
