<?php
declare(strict_types=1);

namespace App\Models;

class DayInfo
{
    private $day;
    private $date;
    private $weekday;
    private $workedHours;
    private $objects;

    public function __construct()
    {
        $this->objects = array();
    }

    public function setDay(string $day): self
    {
        $this->day = $day;
        return $this;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function setWeekDay(string $weekday): self
    {
        $this->weekday = $weekday;
        return $this;
    }

    public function setWorkedHours(int $hours): self
    {
        $this->workedHours = $hours;
        return $this;
    }

    public function setObjects(array $objects): self
    {
        $this->objects = $objects;
        return $this;
    }

    public function getDay(): string
    {
        return $this->day;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getWeekDay(): string
    {
        return $this->weekday;
    }

    public function getWorkedHours(): int
    {
        return $this->workedHours;
    }

    public function getObjects(): array
    {
        return $this->objects;
    }

    public function calculateWorkedHours(): self
    {
        $this->workedHours = 0;
        foreach ($this->objects as $object) {
            $this->workedHours += $object->getHours();
        }
        return $this;
    }
}
