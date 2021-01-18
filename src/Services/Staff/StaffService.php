<?php
declare(strict_types=1);

namespace App\Services\Staff;

use App\Entity\Staff\PeopleDetails;
use App\Repository\PeopleDetailsRepository;

class StaffService
{
    private $peopleDetailsRepository;

    public function __construct(
        PeopleDetailsRepository $peopleDetailsRepository
    ) {
        $this->peopleDetailsRepository = $peopleDetailsRepository;    
    }

    public function savePeopleDetail(PeopleDetails $detail)
    {
        $this->peopleDetailsRepository->save($detail);
    }
}
