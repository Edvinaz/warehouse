<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Entity\Transport\Transport;
use App\Helpers\TransportInfo;
use DateTime;

class TransportFuelManager
{
    /**
     * Transport entity type
     *
     * @var Transport
     */
    protected $transport;

    /**
     * Transport fuel usage
     *
     * @var array
     */
    protected $fuelUsage;

    protected $purchasedMainFuel;

    protected $periodStart;

    public function __construct(Transport $transport) 
    {
        $this->transport = $transport;

        $this->periodStart = $transport->getFuelUsagePeriodStart();
        $this->fuelUsage = $transport->getFuelUsage();
        $this->purchasedMainFuel = $transport->getPurchasedMainFuel();
        $this->setFuelUsageArray();
    }

    /**
     * function to calculate fuelUsage from purchase month till current month
     */
    public function setFuelUsageArray(): Transport
    {
        for($i = 0; $i < $this->getIntervalDifference(); $i++) {
            $date = new DateTime($this->transport->getFuelUsagePeriodStart()->format('Y-m-d'));
            $date->modify("+$i month");
            $month = $date->format('Y-m');

            if (!array_key_exists($month, $this->fuelUsage)) {
                $this->createEmptyFuelUsage($date);
            }
        }
        return $this->transport;
    }

    public function recalculateFuelUsage()
    {
        for($i = 0; $i < $this->getIntervalDifference(); $i++) {
            $date = new DateTime($this->transport->getFuelUsagePeriodStart()->format('Y-m-d'));
            $date->modify("+$i month");
            $month = $date->format('Y-m');

            if (array_key_exists($month, $this->fuelUsage)) {
                $this->recalculateMonthFuelUsage($month);
            }
        }
        return $this->transport;
    }

    private function recalculateMonthFuelUsage(string $month)
    {
        var_dump($month);
        dd($this->purchasedMainFuel);
        dd($this->fuelUsage[$month]);
    }

    private function createEmptyFuelUsage(DateTime $currentMonth): self
    {
        $previousMonth = new DateTime($currentMonth->format('Y-m'));
        $previousMonth->modify('-1 month');

        if (array_key_exists($previousMonth->format('Y-m'), $this->fuelUsage)){
            $mainFuel = $this->getFuelUsageRemainder($previousMonth->format('Y-m'), 'mainFuel');
            $secondaryFuel = $this->getFuelUsageRemainder($previousMonth->format('Y-m'), 'secondaryFuel');
        } else {
            $mainFuel = 0;
            if (is_null($this->transport->getSecondaryFuel())) {
                $secondaryFuel = null;
            } else {
                $secondaryFuel = 0;
            }

        }

        $info = new TransportInfo(
            $currentMonth, 
            $this->transport, 
            $mainFuel, 
            $secondaryFuel,
            $this->fuelUsage
        );
        $this->fuelUsage[$currentMonth->format('Y-m')] = $info->getEmptyUsage();
       
        $this->transport->setMonthFuelUsage($currentMonth->format('Y-m'), $info->getEmptyUsage());

        return $this;
    }

    private function updateFuelUsage(DateTime $currentMonth)
    {
        $info = new TransportInfo(
            $currentMonth, 
            $this->transport, $this->getFuelUsageRemainder($currentMonth->format('Y-m'), 'mainFuel'), 
            $this->getFuelUsageRemainder($currentMonth->format('Y-m'), 'secondaryFuel'),
            $this->fuelUsage
        );
        $this->fuelUsage[$currentMonth->format('Y-m')] = $info->getEmptyUsage();
       
        $this->transport->setMonthFuelUsage($currentMonth->format('Y-m'), $info->getEmptyUsage());

        return $this;
    }

    /**
     * returns fuel remainder for the given month and fuel
     *
     * @param string $month
     * @param string $fuel
     * @return integer|null
     */
    private function getFuelUsageRemainder(string $month, string $fuel): ?int
    {
        if (is_null($this->fuelUsage[$month][$fuel])){
            return null;
        }

        return (int) ($this->fuelUsage[$month][$fuel]['tank'] + $this->fuelUsage[$month][$fuel]['received'] - $this->fuelUsage[$month][$fuel]['used']);
    }

    /**
     * returns integer amount of months of current period
     *
     * @return integer
     */
    private function getIntervalDifference(): int
    {
        $currentDate = new DateTime();
        
        $interval = $currentDate->diff($this->transport->getFuelUsagePeriodStart());

        return (int) $interval->format('%m');
    }
}
