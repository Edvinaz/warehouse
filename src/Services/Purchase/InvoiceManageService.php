<?php
declare(strict_types=1);

namespace App\Services\Purchase;

use Exception;
use App\Services\PurchaseService;
use App\Entity\Purchases\WareInvoices;

class InvoiceManageService extends PurchaseService
{
    public function getNewInvoice(): WareInvoices
    {
        $invoice = new WareInvoices();
        $invoice->setAmount('0');
        $invoice->setVAT('0');

        return $invoice;
    }
    
    /**
     * Save/update invoice.
     */
    public function saveInvoice(WareInvoices $invoice): string
    {
        $this->em->persist($invoice);
        foreach ($invoice->getWarePurchasedMaterials() as $material) {
            $material->setObject($invoice->getObject());
            $this->em->persist($material);
        }

        try {
            $this->em->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return 'Invoice saved';
    }

    public function deleteInvoice(int $invoiceId)
    {
        $invoice = $this->invoicesRepository->find($invoiceId);

        if ($invoice->isDebited()) {
            return 'Cannot delete invoice';
        }
        
        $this->em->remove($invoice);

        try {
            $this->em->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return 'Invoice deleted';
    }
}
