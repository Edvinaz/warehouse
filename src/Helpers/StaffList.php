<?php

namespace App\Helpers;

use App\Repository\StaffRepository;
use App\Interfaces\StaffListInterface;
use App\Interfaces\Staff\StaffInterface;

class StaffList implements StaffListInterface
{
    private $staff;

    public function __construct(StaffRepository $staff)
    {
        $this->setStaff($staff->findAll());
    }

    public function setStaff(array $staff): void
    {
        $this->staff = $staff;
    }

    public function getAllStaff(): array
    {
        $list = [];

        foreach ($this->staff as $staff) {
            if (
                $this->isStaffWorking($staff)
            ) {
                $list[] = $staff;
            }
        }

        return $list;
    }

    public function getResponsible(): array
    {
        $list = [];

        foreach ($this->staff as $staff) {
            if (
                $staff->getResponsible() && $this->isStaffWorking($staff)
            ) {
                $list[] = $staff;
            }
        }

        return $list;
    }

    public function getNotResponsible(): array
    {
        $list = [];

        foreach ($this->staff as $staff) {
            if (
                !$staff->getResponsible() && $this->isStaffWorking($staff)
            ) {
                $list[] = $staff;
            }
        }

        return $list;
    }

    public function getNone(): bool
    {
        return false;
    }

    public function getById(string $id): ?StaffInterface
    {
        foreach ($this->staff as $staff) {
            if ($staff->getId() === intval($id)) {
                return $staff;
            }
        }

        return null;
    }

    private function isStaffWorking($staff): bool
    {
        $date = new DateInterval();
        if (
            is_null($staff->getFired()) ||
            !$staff->getResponsible() && $staff->getFired() >= $date->getBegin() && $staff->getFired() <= $date->getEnd()
        ) {
            return true;
        } else {
            return false;
        }
    }
}
