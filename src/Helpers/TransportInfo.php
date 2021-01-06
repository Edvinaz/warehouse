<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Entity\Transport\Transport;
use DateTime;
use DateTimeInterface;

class TransportInfo
{
    protected $month;

    protected $transport;

    /**
     * Tachometro parodymai mėnesio pradžioje ir pabaigoje.
     */
    protected $tachometerStart;
    protected $tachometerEnd;

    /**
     * pagrindinio ir papildomo kuro bakų likutis mėnesio pradžioje.
     */
    protected $mainTank;
    protected $secondaryTank;

    /**
     * pagrindinio ir papildomo kuro bakų likutis mėnesio pabaigoje.
     */
    protected $mainLeft;
    protected $secondaryLeft;

    /**
     * esamas kuro sunaudojimas
     */
    protected $fuelUsage;

    public function __construct(
        DateTimeInterface $month,
        Transport $transport,
        int $mainFuelRemainder,
        ?int $secondaryFuelRemainder,
        ?array $fuelUsage
    ) {
        $this->month = $month;

        $this->transport = $transport;

        $this->mainLeft = $mainFuelRemainder;
        $this->secondaryLeft = $secondaryFuelRemainder;

        $this->setFuelLimits(10);

        if (is_null($fuelUsage)){
            if (is_null($this->transport->getFuelUsage())) {
                $this->fuelUsage = [];
            } else {
                $this->fuelUsage = $this->transport->getFuelUsage();
            }
        } else {
            $this->fuelUsage = $fuelUsage;
        }
        $this->setAll();
    }

    public function getMonth(): DateTimeInterface
    {
        return $this->month;
    }

    public function getTachometerStart(): int
    {
        return $this->tachometerStart;
    }

    public function getTachometerEnd(): int
    {
        return $this->tachometerEnd;
    }

    public function getMainTank(): int
    {
        return $this->mainTank;
    }

    public function getSecondaryTank(): ?int
    {
        return $this->secondaryTank;
    }

    public function setTachometerEnd(int $tachometer): self
    {
        $this->tachometerEnd = $tachometer;

        return $this;
    }

    public function setMainTank(int $tank): self
    {
        $this->mainTank = $tank;

        return $this;
    }

    public function setSecondaryTank(?int $tank): self
    {
        $this->secondaryTank = $tank;

        return $this;
    }

    public function setFuelLimits(int $limit): self
    {
        $this->mainTank = $limit;
        if (!is_null($this->transport->getSecondaryFuel())) {
            $this->secondaryTank = $limit;
        }

        return $this;
    }

    public function getDistance(): ?int
    {
        if (!\is_null($this->tachometerStart)
            && !\is_null($this->tachometerEnd)
            && $this->tachometerEnd >= $this->tachometerStart
        ) {
            return $this->tachometerEnd - $this->tachometerStart;
        }

        return null;
    }

    public function getEmptyUsage(): array
    {
        $mainReceived = $this->transport->getMonthFuel($this->month->format('Y-m'))[$this->transport->getMainFuel()];
        $mainFuelCalculation = $this->transport->getSecondaryFuelIndex() * ($this->mainTank + $mainReceived - $this->mainLeft);
        if (!\is_null($this->transport->getSecondaryFuel())) {
            $secondaryReceived = $this->transport->getMonthFuel($this->month->format('Y-m'))[$this->transport->getSecondaryFuel()];
            $secondaryFuelCalculation = ($this->secondaryTank + $secondaryReceived - $this->secondaryLeft);
        } else {
            $secondaryReceived = null;
            $secondaryFuelCalculation = 0;
        }

        if ($mainFuelCalculation > 0 || $secondaryFuelCalculation > 0) {
            if ($this->getDistance() === 0) {
                $mainFuelNorm = 0;
            } else {
                $mainFuelNorm = (($mainFuelCalculation + $secondaryFuelCalculation) * 100)
                / ($this->transport->getSecondaryFuelIndex() * $this->getDistance());
            }
            
        } else {
            $mainFuelNorm = 0;
        }

        $mainFuel = [
            'tank' => $this->mainTank,
            'received' => $mainReceived,
            'used' => $this->mainTank + $mainReceived - $this->mainLeft,
            'fuelNorm' => $mainFuelNorm,
        ];
        if (!\is_null($this->transport->getSecondaryFuel())) {
            $secondaryFuel = [
                'tank' => $this->secondaryTank,
                'received' => $secondaryReceived,
                'used' => $this->secondaryTank + $secondaryReceived - $this->secondaryLeft,
                'fuelNorm' => $mainFuelNorm * $this->transport->getSecondaryFuelIndex(),
            ];
        } else {
            $secondaryFuel = null;
        }

        return [
            'mainFuel' => $mainFuel,
            'secondaryFuel' => $secondaryFuel,

            'tachometerStart' => $this->tachometerStart,
            'tachometerEnd' => $this->tachometerEnd,
            'distance' => $this->getDistance(),
        ];
    }

    protected function setAll()
    {
        $list = $this->fuelUsage;
        if (\array_key_exists($this->month->format('Y-m'), $list)) {
            $array = $this->transport->getFuelUsage()[$this->month->format('Y-m')];
            $this->tachometerStart = $array['tachometerStart'];
            $this->tachometerEnd = $array['tachometerEnd'];
            $this->mainTank = $array['mainFuel']['tank'];
            if (!is_null($array['secondaryFuel'])) {
                $this->secondaryTank = $array['secondaryFuel']['tank'];
            } else {
                $this->secondaryTank = null;
            }
        } else {
            $previousMonth = new DateTime($this->month->format('Y-m'));
            $previousMonth->modify('-1 month');

            if (!\is_null($this->transport->getFuelUsage())) {
                if (\array_key_exists($previousMonth->format('Y-m'), $list)) {
                    $this->tachometerStart = $list[$previousMonth->format('Y-m')]['tachometerEnd'];
                    $this->tachometerEnd = $list[$previousMonth->format('Y-m')]['tachometerEnd'];
                    $this->mainTank = (int) ($list[$previousMonth->format('Y-m')]['mainFuel']['tank'] +
                                        $list[$previousMonth->format('Y-m')]['mainFuel']['received'] -
                                        $list[$previousMonth->format('Y-m')]['mainFuel']['used']);
                } else {
                    $this->tachometerStart = 0;
                    $this->tachometerEnd = 0;
                    $this->mainTank = 0;
                }

                if (!\is_null($this->transport->getSecondaryFuel())) {
                    $this->secondaryTank = $list[$previousMonth->format('Y-m')]['secondaryFuel']['tank'] +
                                            $list[$previousMonth->format('Y-m')]['secondaryFuel']['received'] -
                                            $list[$previousMonth->format('Y-m')]['secondaryFuel']['used'];
                }
            } else {
                $this->tachometerStart = $this->transport->getTachometerPurchased();
            }
        }
    }
}
