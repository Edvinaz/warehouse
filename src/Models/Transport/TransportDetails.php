<?php

declare(strict_types=1);

namespace App\Models\Transport;

use App\Helpers\TransportCost;
use App\Helpers\TransportExpense;
use App\Helpers\TransportInfo;
use DateTime;
use DateTimeInterface;
use Exception;
use Symfony\Component\Validator\Constraints\Date;

class TransportDetails
{
    /**
     * Transporto priemonės pagrindinė kuro rūšis.
     */
    protected $mainFuel;

    /**
     * Transporto priemonės papildoma kuro rūšis.
     */
    protected $secondaryFuel;

    /**
     * Transporto priemonės papildomo kuro indeksas lyginant su pagrindiniu kuru.
     */
    protected $secondaryFuelIndex = 1.2;

    /**
     * Transporto priemonės pirkimo data.
     */
    protected $purchased;

    /**
     * Transporto priemonės tachometro parodymai pirkimo metu.
     */
    protected $tachometerPurchased;

    /**
     * Transporto priemonės pardavimo data.
     */
    protected $sold;

    /**
     * Draudimo galijimo pabaiga.
     */
    protected $insurance;

    /**
     * Kelių mokestis apmokėtas iki datos.
     */
    protected $roadTax;

    /**
     * išlaidos automobiliui.
     */
    protected $costs;

    /**
     * kuro pirkimo statistika, pagrindinio ir papildomo kuro atskirai.
     */
    protected $purchasedMainFuel;
    protected $purchasedSecondaryFuel;

    /**
     * kuro bakai.
     */
    protected $mainFuelTank;
    protected $secondaryFuelTank;

    /**
     * kuro sunaudojimas.
     */
    protected $fuelUsage;

    /**
     * kuro sunaudojimo atviro periodo pradžia
     */
    protected $fuelUsagePeriodStart;

    /**
     * default fuel norm
     */
    protected $defaultFuelNorm;

    protected $transportCost;

    public function __construct()
    {
        $this->costs = [];
        $this->tachometerPurchased = 1200;
        $this->transportCost = new TransportExpense();
    }

    public function setMainFuel(int $fuel): self
    {
        $this->mainFuel = $fuel;

        return $this;
    }

    public function setSecondaryFuel(int $fuel): self
    {
        $this->secondaryFuel = $fuel;

        return $this;
    }

    public function getMainFuel(): ?int
    {
        return $this->mainFuel;
    }

    public function getSecondaryFuel(): ?int
    {
        return $this->secondaryFuel;
    }

    public function setPurchased(?DateTimeInterface $date): self
    {
        $this->purchased = $date;

        return $this;
    }

    public function getPurchased(): ?DateTimeInterface
    {
        return $this->purchased;
    }

    public function setTachometerPurchased(?int $tachometer): self
    {
        $this->tachometerPurchased = $tachometer;

        return $this;
    }

    public function getTachometerPurchased(): ?int
    {
        return $this->tachometerPurchased;
    }

    public function getSold(): ?DateTimeInterface
    {
        return $this->sold;
    }

    public function setSold(?DateTimeInterface $sold): self
    {
        $this->sold = $sold;

        return $this;
    }

    public function getInsurance(): ?DateTimeInterface
    {
        return $this->insurance;
    }

    public function setInsurance(?DateTimeInterface $insurance): self
    {
        $this->insurance = $insurance;

        return $this;
    }

    public function setRoadTax(?DateTimeInterface $roadTax): self
    {
        $this->roadTax = $roadTax;

        return $this;
    }

    public function getRoadTax(): ?DateTimeInterface
    {
        return $this->roadTax;
    }

    public function getSecondaryFuelIndex(): float
    {
        if (is_null($this->secondaryFuel)) {
            return 1.2;
        }
        return $this->secondaryFuelIndex;
    }

    public function getCosts(string $month): TransportCost
    {
        if (is_null($this->costs)) {
            $this->costs = [];
        }

        if (\array_key_exists($month, $this->costs)) {
            return $this->costs[$month];
        }

        return new TransportCost();
    }

    public function setCosts(TransportCost $cost, string $date): self
    {
        $this->costs[$date] = $cost;

        return $this;
    }

    public function allCosts()
    {
        $costs = 0;
        if (is_null($this->costs)) {
            $this->costs = [];
        }

        foreach ($this->costs as $cost) {
            $costs += $cost->getAllCosts();
        }

        return $costs;
    }

    public function setTransportExpense(TransportExpense $transport)
    {
        $this->transportCost = $transport;

        return $this;
    }

    public function getTransportExpense(): TransportExpense
    {
        return $this->transportCost;
    }

    /**
     * array $fuel[0: purchased date, 1: fuel type, 2:purchased amount].
     */
    public function setUsedFuel(array $fuel): self
    {
        // dd($fuel);
        switch ($fuel[1]) {
            case $this->mainFuel:
                $this->purchasedMainFuel[$fuel[0]] = [$fuel[2], $fuel[3]];
                $this->mainFuelTank += $fuel[2];
                $this->recalculateMainPurchasedFuel(new DateTime($fuel[0]));
                // $this->updateFuelAmount($fuel, 'mainFuel');

                break;
            case $this->secondaryFuel:
                $this->purchasedSecondaryFuel[$fuel[0]] = $fuel[2];
                $this->secondaryFuelTank += $fuel[2];
                // $this->updateFuelAmount($fuel, 'secondaryFuel');

                break;
            default:
                throw new Exception('wrong fuel type');
        }

        return $this;
    }

    /**
     * recalculates received main fuel
     *
     * @param DateTime $date
     * @return void
     */
    public function recalculateMainPurchasedFuel(DateTime $date)
    {
        $start = new DateTime($date->format('Y-m-1'));
        $end = new DateTime($date->format('Y-m-t'));
        $this->fuelUsage[$start->format('Y-m')]['mainFuel']['received'] = 0;

        foreach ($this->purchasedMainFuel as $key => $value) {
            $purchase = new DateTime($key);
            var_dump($value);

            if ($purchase >= $start && $purchase <= $end) {
                $this->fuelUsage[$purchase->format('Y-m')]['mainFuel']['received'] += $value[0];
            }
        }
    }

    /**
     * recalculates received main fuel
     *
     * @param DateTime $date
     * @return void
     */
    public function recalculateSecondaryPurchasedFuel(DateTime $date)
    {
        $start = new DateTime($date->format('Y-m-1'));
        $end = new DateTime($date->format('Y-m-t'));
        $this->fuelUsage[$start->format('Y-m')]['secondaryFuel']['received'] = 0;

        foreach ($this->purchasedMainFuel as $key => $value) {
            $purchase = new DateTime($key);
            var_dump($value);

            if ($purchase >= $start && $purchase <= $end) {
                $this->fuelUsage[$purchase->format('Y-m')]['secondaryFuel']['received'] += $value[0];
            }
        }
    }

    public function unsetUsedFuel(array $fuel)
    {
        switch ($fuel[1]) {
            case $this->mainFuel:
                $this->mainFuelTank -= $fuel[2];
                if ($this->mainFuelTank < 0) {
                    throw new Exception('not enough fuel in tank');
                }
                unset($this->purchasedMainFuel[$fuel[0]]);

                break;
            case $this->secondaryFuel:
                $this->secondaryFuelTank -= $fuel[2];
                if ($this->secondaryFuelTank < 0) {
                    throw new Exception('not enough fuel in tank');
                }
                unset($this->purchasedSecondaryFuel[$fuel[0]]);

                break;
            default:
                throw new Exception('wrong fuel type');
        }

        return $this;
    }

    /**
     * string $month must be 'YYYY-mm'.
     */
    public function getFuelMonth(string $month)
    {
        $startDate = new DateTime($month.'-01');
        $endDate = new DateTime($startDate->format('Y-m-t'));

        $purchasedFuel = [];
        $purchaseSum = 0;
        
        if (is_null($this->purchasedMainFuel)) {
            $this->purchasedMainFuel = [];
        }
        foreach ($this->purchasedMainFuel as $key => $fuel) {
            $date = new DateTime($key);
            if ($date >= $startDate && $date <= $endDate) {
                $purchaseSum += $fuel[0];
            }
        }
        $purchasedFuel[$this->mainFuel] = $purchaseSum;

        if (!is_null($this->secondaryFuel)) {
            $purchaseSum = 0;
            if (!is_null($this->purchasedSecondaryFuel)) {
                foreach ($this->purchasedSecondaryFuel as $key => $fuel) {
                    $date = new DateTime($key);
                    if ($date >= $startDate && $date <= $endDate) {
                        $purchaseSum += $fuel;
                    }
                }
            }
            $purchasedFuel[$this->secondaryFuel] = $purchaseSum;
        }

        return $purchasedFuel;
    }

    public function getFuelTanks()
    {
        return [$this->mainFuelTank, $this->secondaryFuelTank];
    }

    public function updateUsage(TransportInfo $info)
    {
        $this->fuelUsage[$info->getMonth()->format('Y-m')] = $info->getEmptyUsage();

        return $this;
    }

    /**
     * set fuel usage for given month
     */
    public function setMonthFuelUsage(string $month, array $usage): self
    {
        $this->fuelUsage[$month] = $usage;
        // dd($this->fuelUsage);
        return $this;
    }

    /**
     * tuščia mėnesio ataskaita...
     */
    public function createEmptyUsage(TransportInfo $info)
    {
        $newOne = $info->getEmptyUsage();
        // $newOne = $this->calculateConsumption($newOne, 20, $secondaryTank = 0);

        $this->fuelUsage[$info->getMonth()->format('Y-m')] = $newOne;

        return $this;
    }

    public function getFuelUsage(): ?array
    {
        if (is_null($this->fuelUsage)) {
            return [];
        }

        return $this->fuelUsage;
    }

    /**
     * creates empty fuel usage array.
     */
    protected function EmptyUsage(TransportInfo $info): array
    {
        $mainFuel = [
            'tank' => $info->getMainTank(),
            'received' => $this->getFuelMonth($info->getMonth()->format('Y-m'))[$this->mainFuel],
            'used' => 0,
            'fuelNorm' => 0,
        ];
        if (!is_null($this->secondaryFuel)) {
            $secondaryFuel = [
                'tank' => $info->getSecondaryTank(),
                'received' => $this->getFuelMonth($info->getMonth()->format('Y-m'))[$this->secondaryFuel],
                'used' => 0,
                'fuelNorm' => 0,
            ];
        } else {
            $secondaryFuel = null;
        }

        return [
            'mainFuel' => $mainFuel,
            'secondaryFuel' => $secondaryFuel,

            'tachometerStart' => $info->getTachometerStart(),
            'tachometerEnd' => $info->getTachometerEnd(),
            'distance' => $info->getDistance(),
        ];
    }

    public function setMainFuelTank(int $tank): self
    {
        $this->mainFuelTank += $tank;

        return $this;
    }

    public function setSecondaryFuelTank(int $tank): self
    {
        if (!is_null($this->secondaryFuel)) {
            $this->secondaryFuelTank += $tank;
        }
        
        return $this;
    }

    public function setFuelUsagePeriodStart(DateTime $date)
    {
        $this->fuelUsagePeriodStart = $date;
        return $this;
    }

    public function getFuelUsagePeriodStart(): DateTime
    {
        return $this->fuelUsagePeriodStart;
    }

    /**
     * function to calculate fuelUsage from purchase month till current month
     */
    public function setFuelUsageArray(Transport $transport)
    {
        // dd($this);
        $currentDate = new DateTime();
        
        $interval = $currentDate->diff($this->fuelUsagePeriodStart);

        $diff = (int) $interval->format('%m');

        $previousMonth = new DateTime($this->getFuelUsagePeriodStart()->format('Y-m-d'));

        for($i = 0; $i < $diff; $i++) {
            $date = new DateTime($this->getFuelUsagePeriodStart()->format('Y-m-d'));
            $date->modify("+$i month");
            $month = $date->format('Y-m');

            if (!array_key_exists($month, $this->fuelUsage)) {
                var_dump($month);
                // $this->createEmptyFuelUsage($date, $previousMonth->format('Y-m'), $transport);
            } 
        }
        dd($this);
        dd($this->fuelUsage);
    }

    public function getPurchasedMainFuel(): array
    {
        if (is_null($this->purchasedMainFuel)){
            $this->purchasedMainFuel = [];
        }
        return $this->purchasedMainFuel;
    }

    public function getDefaultFuelNorm(): float
    {
        if (is_null($this->defaultFuelNorm)) {
            return 0;
        }
        return $this->defaultFuelNorm;
    }

    public function setDefaultFuelNorm(float $fuelNorm): self
    {
        $this->defaultFuelNorm = $fuelNorm;

        return $this;
    }

    public function setSecondaryFuelIndex(float $index): self
    {
        $this->secondaryFuelIndex = $index;

        return $this;
    }
}
