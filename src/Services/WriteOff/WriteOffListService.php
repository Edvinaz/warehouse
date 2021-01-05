<?php
declare(strict_types=1);

namespace App\Services\WriteOff;

use App\Services\WriteOffService;

class WriteOffListService extends WriteOffService
{
    public function getWriteOffList()
    {
        return $this->writeOffRepository->getList(
            $this->session->get('interval')->getDate()->format('Y-m-01'),
            $this->session->get('interval')->getDate()->format('Y-m-t')
        );
    }
}
