<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Transport\TransportDetails;

class TransportSummary
{
    private $details;

    public function __construct(TransportDetails $details)
    {
        $this->details = $details;
    }

    public function getDetails(string $month)
    {
        if (!is_null($this->details->getFuelUsage()) && !\array_key_exists($month, $this->details->getFuelUsage())) {
            return [
                'distance' => null,
                'odometerStart' => null,
                'odometerEnd' => null,
                'mainFuelReceived' => null,
                'mainFuelResidue' => null,
                'mainFuelUsed' => null,
                'mainFuelNorm' => null,
                'secondaryFuelReceived' => null,
                'secondaryFuelResidue' => null,
                'secondaryFuelUsed' => null,
                'secondaryFuelNorm' => null,
            ];
        }

        return [
            'distance' => $this->details->getFuelUsage()[$month]['distance'],
            'odometerStart' => $this->details->getFuelUsage()[$month]['tachometerStart'],
            'odometerEnd' => $this->details->getFuelUsage()[$month]['tachometerEnd'],
            'mainFuelReceived' => $this->fuelReceived($this->details->getFuelUsage()[$month]['mainFuel']),
            'mainFuelResidue' => $this->fuelResidue($this->details->getFuelUsage()[$month]['mainFuel']),
            'mainFuelUsed' => $this->fuelUsed($this->details->getFuelUsage()[$month]['mainFuel']),
            'mainFuelNorm' => $this->fuelNorm($this->details->getFuelUsage()[$month]['mainFuel']),
            'secondaryFuelReceived' => $this->fuelReceived($this->details->getFuelUsage()[$month]['secondaryFuel']),
            'secondaryFuelResidue' => $this->fuelResidue($this->details->getFuelUsage()[$month]['secondaryFuel']),
            'secondaryFuelUsed' => $this->fuelUsed($this->details->getFuelUsage()[$month]['secondaryFuel']),
            'secondaryFuelNorm' => $this->fuelNorm($this->details->getFuelUsage()[$month]['secondaryFuel']),
        ];
    }

    private function fuelResidue(?array $fuel): ?int
    {
        if (!is_null($fuel)) {
            return (int) ($fuel['tank'] + $fuel['received'] - $fuel['used']);
        }

        return null;
    }

    private function fuelReceived(?array $fuel): ?float
    {
        if (!is_null($fuel)) {
            return $fuel['received'];
        }

        return null;
    }

    private function fuelNorm(?array $fuel): ?float
    {
        if (!is_null($fuel)) {
            return $fuel['fuelNorm'];
        }

        return null;
    }

    private function fuelUsed(?array $fuel): ?float
    {
        if (!is_null($fuel)) {
            return $fuel['used'];
        }

        return null;
    }
}
