<?php
namespace App\Factories;

use App\Services\Staff\StaffService;
use App\Repository\PeopleDetailsRepository;

class StaffServiceFactory
{
    private $peopleDetailsRepository;

    public function __construct(
        PeopleDetailsRepository $peopleDetailsRepository
    ) {
        $this->peopleDetailsRepository = $peopleDetailsRepository;
    }
    
    public function createStaffServiceManager()
    {
        return new StaffService(
            $this->peopleDetailsRepository
        );
    }

}