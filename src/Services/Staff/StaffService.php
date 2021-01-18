<?php
declare(strict_types=1);

namespace App\Services\Staff;

use App\Repository\PeopleDetailsRepository;

class StaffService
{
    private $peopleDetailsRepository;

    public function __construct(
        PeopleDetailsRepository $peopleDetailsRepository
    ) {
        $this->peopleDetailsRepository = $peopleDetailsRepository;    
    }
}
