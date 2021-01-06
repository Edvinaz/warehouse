<?php

declare(strict_types=1);

namespace App\Services\TimeCard;

use App\Entity\Objects\TimeCard;
use App\Entity\Objects\WareObjects;
use App\Helpers\DateInterval;
use App\Models\TimeCardDay;
use App\Models\Worked;
use App\Models\Worker;
use App\Models\WorkInObject;
use App\Repository\StaffRepository;
use App\Repository\TimeCardRepository;
use DateTime;

class TimeCardService
{
    protected $repository;
    protected $staffRepository;

    protected $list;
    protected $data;

    protected $object;
    protected $objectDetails;

    public function __construct(TimeCardRepository $repository, StaffRepository $staffRepository)
    {
        $this->repository = $repository;
        $this->staffRepository = $staffRepository;
        $this->data = new DateInterval();
    }

    public function setStaff(WareObjects $details)
    {
        $this->list = $details->getEntity()->getStaff();
        $this->object = $details;
        $this->objectDetails = $details->getEntity();

        foreach ($this->list as $worker) {
            $this->setCalendar($worker);
        }

        return $this;
    }

    public function getList()
    {
        return $this->list;
    }

    public function updateWorkDay(int $staff, int $day, int $hours)
    {
        $zero = false;
        $zeroHour = 0;
        $thisDay = $this->repository->getWorkedDay($staff, $this->data->getDay($day));

        if (0 === count($thisDay)) {
            $dayE = new TimeCardDay($day);
            $thisDay = new TimeCard();
            $thisDay->setStaff($this->staffRepository->find($staff));
            $thisDay->setDate(new DateTime($this->data->getDay($day)));
            $thisDay->setHours(0);
            $thisDay->setEntity($dayE);
            $thisDay->setTruancy(false);
            $thisDay->setDisease(false);
            $thisDay->setFreeWacation(false);
            $thisDay->setWacation(false);
        } else {
            $thisDay = $thisDay[0];
        }

        // Updating working hours for current object
        $objectDetails = $this->object->getEntity();
        $objectDetails->setWorkedStaff($staff, $hours, $this->data->getDay($day));
        $this->object->setEntity($objectDetails);

        $entity = $thisDay->getEntity();

        foreach ($entity->getObjects() as $index => $object) {
            if ($object->getId() === $this->object->getId()) {
                $zero = true;
                $zeroHour = $object->getHours();
                $object->setHours(0);
                $entity->unsetObject($index);

                break;
            }
        }

        if (!$zero) {
            $thisDay->addHours($hours);
            $entity->addHours($hours);
            $work = new WorkInObject();
            $work->setId($this->object->getId());
            $work->setName($this->object->getName());
            $work->setHours($hours);
            $entity->setObjects($work);
        } else {
            $thisDay->cutHours($zeroHour);
            $entity->cutHours($zeroHour);
        }

        $thisDay->setEntity($entity);
        $this->repository->save($thisDay);
        $this->setStaff($this->object);

        foreach ($this->list as $workStaff) {
            if ($workStaff->getStaffId() === $staff) {
                return $workStaff->getWorked()[$day - 1];
            }
        }

        return 'true';
    }

    public function updateObjectDetails()
    {
        return true;
    }

    // public function setWorked(int $day)
    // {
    //     $date = new DateTime($this->data->getDay($day));
    //     $worked = new Worked($date, 0);

    //     foreach ($workedDays as $workedOnes) {
    //         if ($workedOnes->getDate()->format('Y-m-d') === $date->format('Y-m-d')) {
    //             $worked->setWorked($workedOnes->getEntity());
    //             $worked->setHours($workedOnes->getEntity()->getHours());
    //         }
    //     }
    //     $worker->setWorked($worked);
    // }

    private function setCalendar(Worker $worker)
    {
        $workedDays = $this->repository->getWorkedDays($worker->getStaffId(), $this->data->getBegin());
        foreach ($this->data->getDateIntervalDays() as $oneDay) {
            $date = new DateTime($oneDay->getYear().'-'.$oneDay->getMonth().'-'.$oneDay->getDay());
            $worked = new Worked($date, 0);

            foreach ($workedDays as $workedOnes) {
                if ($workedOnes->getDate()->format('Y-m-d') === $date->format('Y-m-d')) {
                    $worked->setWorked($workedOnes->getEntity());
                    $worked->setHours($workedOnes->getEntity()->getHours());
                }
            }
            $worker->setWorked($worked);
        }

        return true;
    }
}
