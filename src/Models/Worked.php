<?php
declare(strict_types=1);

namespace App\Models;

use DateTime;

class Worked 
{
    private $day;
    private $hours;
    private $weekDay;
    private $date;

    private $worked;

    public function __construct(DateTime $day, int $hours)
    {
        $this->day = $day;
        $this->date = $day->format('Y-m-d');
        $this->weekDay = $day->format('N');
        $this->hours = $hours;
        $this->worked = array();
    }

    public function setHours(int $hours): self
    {
        $this->hours = $hours;
        return $this;
    }

    public function setWorked($worked)
    {
        $this->worked = $worked;
    }

    public function getWorked()
    {
        return $this->worked;
    }

    public function getHours(): int
    {
        return $this->hours;
    }

    public function getDay(): DateTime
    {
        return $this->day;
    }

    public function getDayString(): string
    {
        return $this->day->format('d');
    }

    public function getWeekDay(): string
    {
        return $this->weekDay;
    }

    public function getDate(): string
    {
        return $this->date;
    }
}
