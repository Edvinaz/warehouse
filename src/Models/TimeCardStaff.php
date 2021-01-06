<?php
declare(strict_types=1);

namespace App\Models;

use App\Entity\Staff\WorkerModel;

class TimeCardStaff
{
    protected $person;
    protected $workDays;

    public function __construct(WorkerModel $person)
    {
        $this->person = $person;
    }

    public function getPerson()
    {
        return $this->person;
    }

    public function getWorkDays()
    {
        return $this->workDays;
    }

    public function setWorkDays(array $days)
    {
        $this->workDays = $days;
    }

    public function replaceWorkDay(TimeCardDay $timeCardDay): self
    {
        $index = $timeCardDay->getDay() - 1;

        $this->workDays[$index] = $timeCardDay;

        return $this;
    }
}
