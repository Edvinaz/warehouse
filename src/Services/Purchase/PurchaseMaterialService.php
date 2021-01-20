<?php
declare(strict_types=1);

namespace App\Services\Purchase;

use Exception;
use App\Services\PurchaseService;
use App\Entity\Purchases\WarePurchasedMaterials;

class PurchaseMaterialService extends PurchaseService
{
    /**
     * @return WarePurchasedMaterials
     */
    public function createPurchaseMaterialFormModel(int $invoiceID, int $materialID): WarePurchasedMaterials
    {
        $purchase = new WarePurchasedMaterials();

        /**
         * @var WareInvoices
         */
        $invoice = $this->invoicesRepository->find($invoiceID);

        $purchase->setInvoice($invoice);
        if ($invoice->getObject()) {
            $purchase->setObject($invoice->getObject());
        }

        /**
         * @var WareMaterials
         */
        $material = $this->materialRepository->find($materialID);

        $purchase->setMaterial($material);

        return $purchase;
    }

    public function saveNewMaterial(WarePurchasedMaterials $material, int $newMaterial): void
    {
        $material->setBalance($material->getQuantity());

        if($this->checkMaterialDifference($material)) {
            return;
        }

        if (0 === $newMaterial) {
            $newMaterial = $material->getMaterial();
            $this->em->persist($newMaterial);
        }

        $this->em->persist($material);
        $this->em->flush();
    }

    public function getMaterialPurchase(int $purchaseId): WarePurchasedMaterials
    {
        return $this->purchasedMaterialsRepository->find($purchaseId);
    }

    /**
     * Save/update purchased material.
     */
    public function updatePurchasedMaterial(WarePurchasedMaterials $material): string
    {
        $material->setBalance($material->getQuantity());

        try {
            $this->em->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return 'Successfully updated';
    }

    public function deletePurchasedMaterial(int $purchasedMaterialId): string
    {
        $purchasedMaterial = $this->purchasedMaterialsRepository->find($purchasedMaterialId);

        if (count($purchasedMaterial->getWareDebitedMaterials()) > 0) {
            return 'Cannot delete, because already debited';
        }
        
        try {
            $this->em->remove($purchasedMaterial);
            $this->em->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $this->em->flush();

        return 'Successfully deleted';
    }

    private function checkMaterialDifference(WarePurchasedMaterials $material) : bool
    {
        $last = $this->purchasedMaterialsRepository->getLastInserted()[0];
        if (
            $last->getInvoice() === $material->getInvoice() 
            && $last->getMaterial() === $material->getMaterial() 
            && (float) $last->getQuantity() === (float) $material->getQuantity() 
            && (float) $last->getPrice() === (float) $material->getPrice()
        ) {
            return true;
        } else {
            return false;
        }
        return true;
    }
}
