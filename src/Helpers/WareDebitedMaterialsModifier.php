<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Entity\Debition\WareDebitedMaterials;
use App\Entity\Debition\WareWriteOffs;

/**
 * When debitting material (persist/update-increase):
 *      WriteOff 'amount' increasing
 *      ObjectDetails 'reservedMaterials' decrease if material was reserved
 *      ObjectDetails 'debitedMaterials' increasing.
 *  When debitting material (update-decrease):
 *      WriteOff 'amount' decreasing
 *      ObjectDetails 'reservedMaterials' increase if material was reserved
 *      ObjectDetails 'debitedMaterials' decreasing.
 */
class WareDebitedMaterialsModifier
{
    private $debitedMaterial;

    public function __construct(WareDebitedMaterials $wareDebitedMaterials)
    {
        $this->debitedMaterial = $wareDebitedMaterials;
    }

    public function persist()
    {
        $writeOff = $this->updateWriteOff($this->debitedMaterial->getTotalSum());
        $this->debitedMaterial->setWriteoff($writeOff);

        return $this->debitedMaterial;
    }

    public function update()
    {
        $events = $this->debitedMaterial->popEvents();
        // when updating && debit sum increase
        if (\array_key_exists('update', $events)) {
            $writeOff = $this->updateWriteOff($events['update']);
            $this->debitedMaterial->setWriteoff($writeOff);
        }

        return $this->debitedMaterial;
    }

    public function remove()
    {
        $events = $this->debitedMaterial->popEvents();
        // when updating && debit sum increase
        $writeOff = $this->debitedMaterial->getWriteoff();
        if (\array_key_exists('update', $events)) {
            $writeOff = $this->updateWriteOff($events['update']);
        }
        $this->debitedMaterial->setWriteoff($writeOff);

        return $this->debitedMaterial;
    }

    private function updateWriteOff(string $amount): WareWriteOffs
    {
        $writeOff = $this->debitedMaterial->getWriteoff();
        $material = $this->debitedMaterial->getPurchase();
        $object = $writeOff->getObject();

        if ($material->isReserved()) {
            $object->updateDebitedMaterialsFromReservedMaterials($amount);
        } else {
            $object->updateDebitedMaterials($amount);
        }
        $writeOff->setObject($object);
        $writeOff->increaseAmount($amount);

        return $writeOff;
    }
}
