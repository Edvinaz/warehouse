<?php

declare(strict_types=1);

namespace App\Models;

use Symfony\Component\HttpFoundation\Session\Session;

class TimeCardDay
{
    protected $year;
    protected $month;
    protected $day;

    protected $hours;
    protected $objects;
    protected $staff;
    protected $testIT;

    public function __construct(int $day)
    {
        $session = new Session();
        $this->year = $session->get('interval')->getDate()->format('Y');
        $this->month = $session->get('interval')->getDate()->format('m');
        $this->day = $day;
        $this->objects = [];
        $this->hours = 0;
    }

    public function getYear()
    {
        return $this->year;
    }

    public function getMonth()
    {
        return $this->month;
    }

    public function getDay()
    {
        return $this->day;
    }

    public function getHours()
    {
        return $this->hours;
    }

    public function nullHours(): self
    {
        $this->hours = 0;

        return $this;
    }

    public function addHours(int $hours): self
    {
        $this->hours += $hours;

        return $this;
    }

    public function cutHours(int $hours): self
    {
        $this->hours -= $hours;

        return $this;
    }

    public function setObjects(WorkInObject $objects)
    {
        $this->objects[] = $objects;
        $this->countHours();
    }

    public function unsetObject(int $index)
    {
        unset($this->objects[$index]);

        return $this;
    }

    public function getObjects()
    {
        return $this->objects;
    }

    public function countHours()
    {
        $this->hours = 0;

        foreach ($this->objects as $object) {
            $this->addHours(intval($object->getHours()));
        }
    }

    public function getStaff()
    {
        return $this->staff;
    }
}
