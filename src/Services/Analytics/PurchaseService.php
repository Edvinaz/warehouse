<?php
declare(strict_types=1);

namespace App\Services\Analytics;

use App\Helpers\Analytics\PurchaseContrahentModel;
use App\Repository\Analytics\PurchaseInvoicesRepository;
use App\Repository\Analytics\PurchaseMaterialsRepository;
use App\Services\PurchaseService as ServicesPurchaseService;

class PurchaseService extends ServicesPurchaseService
{
    private $purchaseAnalyticsRepository;
    private $invoiceAnalyticsRepository;

    public function __construct(PurchaseMaterialsRepository $repository, PurchaseInvoicesRepository $invoiceRepository)
    { 
        $this->purchaseAnalyticsRepository = $repository;
        $this->invoiceAnalyticsRepository = $invoiceRepository;
    }
    public function purchaseStatus()
    {
        $invoices = $this->invoiceAnalyticsRepository->getMonthPurchases();

        $list = [];
        foreach ($invoices as $invoice) {
            $newInvoice = new PurchaseContrahentModel();
            $newInvoice->setName($invoice['name']);
            $newInvoice->setAmount($invoice['amount']);
            $newInvoice->setId($invoice['id']);
            $newInvoice->setMaterials(
                $this->purchaseAnalyticsRepository->getMonthPurchase($invoice['id'])
            );

            $list[] = $newInvoice;
        }
        return $list;
    }

    public function contrahentPurchaseStatus(int $id)
    {
        $list = $this->invoiceAnalyticsRepository->getContrahentMonthPurchases($id);
        $foreach = [];
        foreach ($list as $item) {
            $newInvoice = new PurchaseContrahentModel();
            $newInvoice->setName($item->getNumber());
            $newInvoice->setAmount($item->getAmount());
            $newInvoice->setMaterials(
                $this->purchaseAnalyticsRepository->getInvoiceMaterials($item->getId())
            );
            $foreach[] = $newInvoice;
        }
        return $foreach;
    }
}