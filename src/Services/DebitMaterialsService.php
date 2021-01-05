<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Debition\WareDebitedMaterials;
use App\Entity\Purchases\WarePurchasedMaterials;
use App\Helpers\DebitMaterialHelper;
use App\Repository\WareDebitedMaterialsRepository;
use App\Repository\WarePurchasedMaterialsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Session\Session;

class DebitMaterialsService
{
    protected $entityManager;
    protected $debitedMaterialRepository;
    protected $purchasedMaterialRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        WarePurchasedMaterialsRepository $purchasedMaterialRepository,
        WareDebitedMaterialsRepository $debitedMaterialRepository
    ) {
        $this->entityManager = $entityManager;
        $this->purchasedMaterialRepository = $purchasedMaterialRepository;
        $this->debitedMaterialRepository = $debitedMaterialRepository;
    }

    /**
     * @return void
     */
    public function debitMaterial(DebitMaterialHelper $debitedMaterial)
    {
        $session = new Session();

        if ($session->get('reserved')) {
            $list = $this->purchasedMaterialRepository->getReservedMaterialsForDebitting($debitedMaterial);
        } else {
            $list = $this->purchasedMaterialRepository->getMaterialsForDebitting($debitedMaterial);
        }
        $debited = $debitedMaterial->getAmount();
        try {
            foreach ($list as $item) {
                if ($debited > 0) {
                    if (floatval($item->getBalance()) - floatval($debited) >= 0) {
                        $this->updateDebitedMaterial($item, (string) $debited, $debitedMaterial);

                        $debited = 0;
                    } else {
                        $debited = $debited - $item->getBalance();

                        $this->updateDebitedMaterial($item, $item->getBalance(), $debitedMaterial);
                    }
                }
            }
       } catch (Exception $e) {
           dd($e);
           return false;
       }
        $this->entityManager->flush();
    }

    public function unDebitMaterial(DebitMaterialHelper $debitMaterial): void
    {
        $list = $this->debitedMaterialRepository->getDebitedMaterialList($debitMaterial->getMaterial()->getId(), $debitMaterial->getWriteOff()->getId());
        $amount = $debitMaterial->getAmount();

        foreach ($list as $item) {
            if ($amount > 0) {
                if ($item->getAmount() - $amount >= 0) {
                    $this->updateUnDebitedMaterial($item, (string) $amount, $debitMaterial);
                    $amount = 0;
                } else {
                    $amount -= $item->getAmount();
                    $this->updateUnDebitedMaterial($item, (string) $item->getAmount(), $debitMaterial);
                }
            }
            $this->entityManager->flush();
        }
    }

    protected function updateDebitedMaterial(
        WarePurchasedMaterials $item,
        string $debited,
        DebitMaterialHelper $debitedMaterial
    ): void {
        $item->decreaseBalance($debited);

        $debit = $this->debitedMaterialRepository->getDebitedMaterialList($item->getMaterial()->getId(), $debitedMaterial->getWriteOff()->getId());
        $writeOff = $debitedMaterial->getWriteOff();

        if (!$debit) {
            $debit = new WareDebitedMaterials();
            $debit->setAmount($debited);
            $debit->setPurchase($item);
            $debit->setWriteoff($debitedMaterial->getWriteOff());
        } else {
            $debit = $debit[0];
            $debit->increaseAmount($debited);
        }

        $this->entityManager->persist($item);
        $this->entityManager->persist($debit);
        $this->entityManager->persist($writeOff);
    }

    protected function updateUnDebitedMaterial(
        WareDebitedMaterials $material,
        string $amount,
        DebitMaterialHelper $debitMaterial
    ): void {
        $purchase = $this->purchasedMaterialRepository->find($material->getPurchase());
        $writeOff = $debitMaterial->getWriteOff();

        $purchase->increaseBalance($amount);

        $material->decreaseAmount($amount);

        if ($material->isAmountZero()) {
            $this->entityManager->remove($material);
        } else {
            $this->entityManager->persist($material);
        }
        $this->entityManager->persist($purchase);
        $this->entityManager->persist($writeOff);
    }
}
