<?php
declare(strict_types=1);

namespace App\Models;

use DateTime;

class ObjectWorkDay
{
    protected $date;
    protected $hours;
    protected $staff;

    public function __construct()
    {
        $this->hours = 0;
        $this->staff = array();
    }

    public function setDate(DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function addHours(int $hour): self
    {
        $this->hours += $hour;

        return $this;
    }

    public function cutHours(int $hour): self
    {
        $this->hours -= $hour;

        return $this;
    }

    public function getHours(): int
    {
        return $this->hours;
    }

    public function setStaff($person)
    {
        if (count($this->staff) === 0) {
            $this->staff[] = $person;
        } else {
            for ($i = 0; $i < count($this->staff); $i++) {
                $a = $i;
            }
        }
    }
}
