<?php
declare(strict_types=1);

namespace App\Models;

use DateTime;
use DatePeriod;
use DateInterval;
use App\Helpers\DateInterval as DI;

class TimeCardApi
{
    private $name;
    private $id;
    private $month;

    public function __construct(Worker $worker, array $workedDays)
    {
        $this->name = $worker->getName();
        $this->id = $worker->getStaffId();
        $this->setmonth($workedDays);
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    private function setMonth(array $workedDays)
    {
        $beginTime = new DateTime(DI::getSbegin()->format('Y-m-d'));
        $endTime = new DateTime(DI::getSend()->format('Y-m-d'));
        $endTime->modify('+1 day');
        $dateRange = new DatePeriod(
                         $beginTime,
                         new DateInterval('P1D'),
                         $endTime
                    );
        
        foreach($dateRange as $date){
            $datos = new DayInfo();
            $datos->setDay($date->format('d'));
            $datos->setDate($date->format('Y-m-d'));
            $datos->setWeekday(date('w', strtotime($datos->getDate())));

            $update = true;
            
            foreach ($workedDays as $day) {
                if ($day->getDate() == $date) {
                    $datos->setWorkedHours($day->getHours());
                    $datos->setObjects($day->getEntity()->getObjects());
                    $update = false;
                }
            }
            if ($update) {
                $datos->setWorkedHours(0);
            } else {
                $datos->calculateWorkedHours();
            }
            $this->month[] = $datos;
        }
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMonth(): array
    {
        return $this->month;
    }
}