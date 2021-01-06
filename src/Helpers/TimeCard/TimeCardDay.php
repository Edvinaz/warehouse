<?php
declare(strict_types=1);

namespace App\Helpers\TimeCard;

class TimeCardDay
{
    protected $day;
    protected $workHours;

    public function __construct(array $day)
    {
        if (count($day) > 0) {
            $this->workHours = $day[0]->getHours();
        } else {
            $this->workHours = 0;
        }
        $this->day = $day;
    }

    public function getWorkHours(): int
    {
        return $this->workHours;
    }
}