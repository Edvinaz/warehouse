<?php

namespace App\Interfaces;

interface FuelUsageInterface
{
    /**
     * get tachometer at the beginning of the month
     *
     * @return integer
     */
    public function getTachometerBegin(): int;

    /**
     * get tachometer ar the end of the month
     *
     * @return integer
     */
    public function getTachometerEnd(): int;

    /**
     * get distance driven
     *
     * @return integer
     */
    public function getDistance(): int;

    public function getMainFuelBegin(): float;
    public function getMainFuelReceived(): float;
    public function getMainFuelUsed(): float;
    public function getMainFuelEnd(): float;
    public function getMainFuelNorm(): float;

    public function getSecondaryFuelBegin(): float;
    public function getSecondaryFuelReceived(): float;
    public function getSecondaryFuelUsed(): float;
    public function getSecondaryFuelEnd(): float;
    public function getSecondaryFuelNorm(): float;
}
