<?php

namespace App\Interfaces;

use App\Interfaces\Staff\StaffInterface;

interface StaffListInterface
{
    /** Set all staff */
    public function setStaff(array $staff): void;

    /** Get all setted staff */
    public function getAllStaff(): array;

    /** Get all responsible staff (manager & etc.) */
    public function getResponsible(): array;

    /** Get all workers that is nor responsible */
    public function getNotResponsible(): array;

    /** Get worker by staff ID */
    public function getById(string $id): ?StaffInterface;

}