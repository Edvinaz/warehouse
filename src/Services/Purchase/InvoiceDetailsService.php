<?php
declare(strict_types=1);

namespace App\Services\Purchase;

use App\Models\PurchaseInvoice;
use App\Services\PurchaseService;

class InvoiceDetailsService extends PurchaseService
{
    protected $purchaseInvoice;
    protected $materialsList;

    public function getPurchaseInvoice(): PurchaseInvoice
    {
        if (is_null($this->purchaseInvoice)) {
            $this->setPurchaseInvoice();
        }
        return $this->purchaseInvoice;
    }

    public function setMaterialList(string $search = '')
    {
        $this->materialsList = $this->materialRepository->getPaginatedMaterialsList($search);
        $this->purchaseInvoice->setMaxMaterialsPage(intval(count($this->materialsList) / $this->purchaseInvoice->getPageSize()));
    }

    /**
     * Get materials list.
     */
    public function getMaterialsList()
    {
        if (is_null($this->materialsList)) {
            $this->setMaterialList();
        }
        return $this->materialsList;
    }

    private function setPurchaseInvoice(): self
    {
        $this->purchaseInvoice = new PurchaseInvoice();

        return $this;
    }
}