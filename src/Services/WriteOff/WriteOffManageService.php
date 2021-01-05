<?php
declare (strict_types = 1);

namespace App\Services\WriteOff;

use App\Entity\Debition\WareWriteOffs;
use App\Services\WriteOffService;
use App\Settings\Settings;
use DateTime;

class WriteOffManageService extends WriteOffService
{
    public function newWriteOff(): string
    {
        $date = $this->session->get('interval')->getDate();

        if (!$this->writeOffRepository->checkWriteOff($date->format('Y-m-t'))) {
            $object = $this->objectsRepository->find(Settings::OWN_OBJECT);
            $writeOff = new WareWriteOffs();
            $writeOff->setNumber($date->format('Y') . '/' . $date->format('m'));
            $writeOff->setAmount(0);
            $writeOff->setDate(new DateTime($date->format('Y-m-t')));
            $writeOff->setObject($object);
            $this->writeOffRepository->save($writeOff);

            return 'Write Off created';
        }

        return 'Write Off already exist';
    }

    public function newDebitMaterial(int $materialID, bool $isMaterialDebiting)
    {
        return $this->debitMaterialHelper->createNew($materialID, $this->entity, $isMaterialDebiting);
    }
}
