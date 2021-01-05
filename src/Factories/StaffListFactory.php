<?php

namespace App\Factories;

use App\Helpers\StaffList;
use App\Repository\StaffRepository;

class StaffListFactory
{
    private $em;

    public function __construct(StaffRepository $em)
    {
        $this->em = $em;
    }
    
    public function createStaffListManager(): StaffList
    {
        $staffListManager = new StaffList();
        $list = $this->em->findAll();
        $staffListManager->setStaff($list);

        return $staffListManager;
    }

}