<?php
declare(strict_types=1);

namespace App\Services\TimeCard;

use DateTime;
use App\Helpers\DateInterval;
use App\Repository\StaffRepository;
use App\Helpers\TimeCard\TimeCardDay;
use App\Repository\TimeCard\TimeCardSummary;

class TimeCardSummaryService extends TimeCardService
{
    protected $timeCardRepository;
    protected $staffRepository;

    public function __construct(TimeCardSummary $rep, StaffRepository $staff) 
    {
        $this->timeCardRepository = $rep;
        $this->staffRepository = $staff;
    }

    public function getList(): array
    {
        $staffList = $this->getAvailableStaff();

        return $this->currentStaffList($staffList);
    }

    private function getAvailableStaff(): array
    {
        $date = new DateInterval();

        $staff = $this->staffRepository->getAvailableStaff();
        $staffList = [];

        foreach ($staff as $man) {
            $acceptedDiff = $man->getAccepted()->diff($date->getEnd());
            if (!is_null($man->getFired())) {
                $firedDiff = $man->getFired()->diff($date->getBegin());
            }

            if (
                is_null($man->getFired()) && $acceptedDiff->format('%R%a') > 0 
                || !is_null($man->getFired()) && $firedDiff->format('%R%a') < 0
            ){
                $staffList[] = $man;
            }
        }

        return $staffList;
    }

    private function currentStaffList(array $staffList): array
    {
        $list = [];

        foreach ($staffList as $worker) {
            $list[$worker->getFullName()] = $this->getCalendar($worker->getId());
        }

        return $list;
    }

    private function getCalendar(int $staffId)
    {
        $date = new DateInterval();

        $dateArray = $date->getDays();
        $array = [];

        for ($i=1; $i<=$dateArray; $i++) {
            $da = new DateTime($date->getEnd()->format('Y-m').'-'.$i);
            $array[$i] = new TimeCardDay($this->timeCardRepository->getWorkedDay($staffId, $da->format('Y-m-d')));
        }
      
        return $array;
    }
}