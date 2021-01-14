<?php

declare(strict_types=1);

namespace App\Models;

class PurchaseInvoice
{
    protected $invoiceId;
    protected $materialSearch;

    protected $currentMaterialsPage;
    protected $maxMaterialsPage;
    protected $pageSize;

    protected $currentPurchasedPage;
    protected $maxPurchasedPage;

    public function __construct(string $materialsSearch = '')
    {
        $this->materialSearch = $materialsSearch;
        $this->currentMaterialsPage = 0;
        $this->pageSize = 25;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function setInvoiceId(int $invoiceId): self
    {
        $this->invoiceId = $invoiceId;

        return $this;
    }

    public function setMaterialSearch(string $materialSearch): self
    {
        $this->materialSearch = $materialSearch;

        return $this;
    }

    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }

    public function getMaterialSearch(): string
    {
        return $this->materialSearch;
    }

    public function getCurrentMaterialsPage(): int
    {
        return $this->currentMaterialsPage;
    }

    public function setCurrentMaterialsPage(int $currentPage): self
    {
        $this->currentMaterialsPage = $currentPage;

        return $this;
    }

    public function getMaxMaterialsPage(): int
    {
        if (is_null($this->maxMaterialsPage)) {
            return 0;
        }
        return $this->maxMaterialsPage;
    }

    public function setMaxMaterialsPage(int $maxPage): self
    {
        $this->maxMaterialsPage = $maxPage;

        return $this;
    }

    public function getCurrentPurchasedPage(): int
    {
        return $this->currentPurchasedPage;
    }

    public function setCurrentPurchasedPage(int $currentPage): self
    {
        $this->currentPurchasedPage = $currentPage;

        return $this;
    }

    public function getMaxPurchasedPage(): int
    {
        return $this->maxPurchasedPage;
    }

    public function setMaxPurchasedPage(int $maxPage): self
    {
        $this->maxPurchasedPage = $maxPage;

        return $this;
    }
}
