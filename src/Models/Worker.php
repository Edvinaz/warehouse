<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeInterface;
use App\Interfaces\Staff\StaffInterface;

class Worker 
{
    private $name;
    private $staffId;
    private $worked; 

    private $workedDay;

    public function __construct(StaffInterface $staff)
    {
        $this->name = $staff->getPerson()->getFullName();
        $this->staffId = $staff->getId();
        $this->worked = array();
        $this->workedDay = array();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStaffId(): int
    {
        return $this->staffId;
    }

    public function setWorked(Worked $worked)
    {
        $add = true;
        if (is_null($this->worked)) {
            $this->worked = array();
        }
        foreach ($this->worked as $day) {
            if ($worked->getDay() == $day->getDay()) {
                $add = false;
                break;
            }
        }
        if ($add) {
            $this->worked[] = $worked;
        }
        return $this;
    }

    public function getWorked()
    {
        return $this->worked;
    }

    public function getWorkedDay()
    {
        return $this->workedDay;
    }

    public function setWorkedDay(DateTimeInterface $day, int $hours)
    {
        if (is_null($this->workedDay)) {
            $this->workedDay = array();
        }
        if (array_key_exists($day->format('Y-m-d'), $this->workedDay)) {
            unset($this->workedDay[$day->format('Y-m-d')]);
        } else {
            $this->workedDay[$day->format('Y-m-d')] = $hours;
        }
        
        return $this;
    }

    public function getHours()
    {
        if (is_null($this->workedDay)){
            $this->workedDay = array();
        }
        return array_sum($this->workedDay);
    }
}
