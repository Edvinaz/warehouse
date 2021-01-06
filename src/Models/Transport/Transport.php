<?php
declare(strict_types=1);

namespace App\Models\Transport;

use App\Entity\Transport\MonthlyUsage;

abstract class Transport
{
    private $mainFuel;
    private $secondaryFuel;

    private $usage;

    public function setFuel(TransportDetails $fuel): self
    {
        $this->mainFuel = new FuelStatus($fuel->getMainFuel());

        if ($fuel->getSecondaryFuel() === NULL) {
            unset($this->secondaryFuel);
        } else {
            $this->secondaryFuel = new FuelStatus($fuel->getMainFuel());
        }
        return $this;
    }

    public function getMainFuel()
    {
        return $this->mainFuel;
    }

    public function getSecondaryFuel()
    {
        return $this->secondaryFuel;
    }

    public function setUsage(MonthlyUsage $monthlyUsage)
    {
        $this->usage = $monthlyUsage;
        return $this;
    }

    public function getUsage(): ?MonthlyUsage
    {
        return $this->usage;
    }
}
