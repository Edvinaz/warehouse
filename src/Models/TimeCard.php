<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\DateInterval;

class TimeCard extends ObjectModel
{
    protected $dateInterval;

    protected $days;

    protected $allStaff;

    public function __construct()
    {
        $this->dateInterval = new DateInterval();
        $this->setDays();
    }

    public function setAllStaff(array $staff)
    {
        $this->allStaff = $staff;
    }

    public function getDays()
    {
        return $this->days;
    }

    public function getAllStaff()
    {
        return $this->allStaff;
    }

    protected function setDays()
    {
        $this->days = $this->dateInterval->getIntervalDays();
    }
}
