<?php
declare(strict_types=1);

namespace App\Services\WriteOff;

use App\Services\WriteOffService;
use Symfony\Component\HttpFoundation\Session\Session;

class WriteOffDetailsService extends WriteOffService
{
    public function getDebitedMaterials(int $writeOffId)
    {
        return $this->debitedMaterialsRepository->getWriteOffMaterialList($writeOffId);
    }

    public function getWarehouseMaterials(int $writeOffId, ?array $search)
    {
        $session = new Session();

        $writeOff = $this->getWriteOff($writeOffId);

        if ($writeOff->getObject() && $writeOff->getObject()->hasReserved() && $session->get('reserved')) {
            $session->set('reserved', true);
            return $this->purchasedMaterialsRepository->getObjectReservedMaterials($writeOff->getObject(), $search);
        } else {
            $session->set('reserved', false);
            return $this->purchasedMaterialsRepository->getNotReservedMaterials($search);
        }
    }
}
