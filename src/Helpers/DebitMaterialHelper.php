<?php

declare(strict_types=1);

namespace App\Helpers;

use Exception;
use App\Entity\Debition\WareWriteOffs;
use App\Entity\Materials\WareMaterials;
use App\Repository\WareMaterialsRepository;
use App\Repository\WareDebitedMaterialsRepository;
use App\Repository\DebitMaterials\PurchasedMaterials;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints as Assert;

class DebitMaterialHelper
{
    protected $purchasedMaterialsRepository;
    protected $materialRepository;
    protected $debitedRepository;
    protected $session;

    protected $material;
    protected $writeOff;

    protected $availableAmount;
    protected $price;

    /**
     * @Assert\LessThanOrEqual(propertyPath="availableAmount")
     */
    protected $amount;

    public function __construct(
        WareMaterialsRepository $materialRepository,
        ?PurchasedMaterials $purchasedMaterials,
        WareDebitedMaterialsRepository $debitedMaterials
    ) {
        $this->purchasedMaterialsRepository = $purchasedMaterials;
        $this->materialRepository = $materialRepository;
        $this->debitedRepository = $debitedMaterials;

        $this->session = new Session();
    }

    public function createNew(
        int $materialId,
        WareWriteOffs $writeOff,
        bool $isMaterialDebiting
    ) {
        $this->material = $this->materialRepository->find($materialId);
        $this->writeOff = $writeOff;

        if ($isMaterialDebiting) {
            $this->startMaterialDebit();
        } else {
            $this->startMaterialUnDebit();
        }

        return $this;
    }

    public function setMaterial(WareMaterials $material): void
    {
        $this->material = $material;
    }

    public function getMaterial(): WareMaterials
    {
        return $this->material;
    }

    public function getAmount(): ?string
    {
        return (string) $this->amount;
    }

    public function setAmount(string $amount): void
    {
        $this->amount = floatval($amount);
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): void
    {
        $this->price = $price;
    }

    public function getAvailableAmount(): string
    {
        return (string) $this->availableAmount;
    }

    public function setAvailableAmount(string $amount)
    {
        $this->availableAmount = floatval($amount);
    }

    public function getWriteOff()
    {
        return $this->writeOff;
    }

    protected function getReservedMaterialsSum()
    {
        $material = $this->purchasedMaterialsRepository->getReservedMaterialForDebit($this->material->getId(), $this->writeOff->getObject()->getId());
        if (is_null($material)) {
            return null;
        }
        $this->price = number_format(floatval($material['price']), 2, ',', '');
        $this->setAvailableAmount(number_format(floatval($material['quantity']), 2, ',', ''));
        return true;
    }

    protected function getNotReservedMaterialsSum()
    {
        $material = $this->purchasedMaterialsRepository->getMaterialForDebit($this->material->getId());
        if (is_null($material)) {
            return null;
        }   
        $this->price = number_format(floatval($material['price']), 2, ',', '');
        $this->availableAmount = (string) number_format(floatval($material['quantity']), 2, ',', '');
        return true;
    }

    protected function getDebitedMaterialForUpdate(): void
    {
        $material = $this->debitedRepository->getDebitedMaterial($this->material->getId(), $this->writeOff->getId())[0];
        $this->price = number_format(floatval($material['price']), 2, ',', '');
        $this->availableAmount = (string) number_format(floatval($material['amount']), 2, ',', '');
    }

    protected function startMaterialDebit(): void
    {
        if ($this->session->get('reserved')) {
            if (is_null($this->getReservedMaterialsSum())) {
                // throw new Exception('Wrong');
            }
        } else {
            $this->getNotReservedMaterialsSum();
        }
    }

    protected function startMaterialUnDebit(): void
    {
        $this->getDebitedMaterialForUpdate();
    }
}
