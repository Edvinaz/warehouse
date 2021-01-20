<?php
namespace App\Tests\services\purchase;

use PHPUnit\Framework\TestCase;
use App\Entity\Purchases\WareInvoices;
use App\Services\Purchase\InvoiceManageService;
use App\Tests\Traits\ServiceTrait;

class InvoiceManageServiceTest extends TestCase
{
    use ServiceTrait;

    public function testGetNewInvoice()
    {
        $this->setPurchaseServiceRepository();
        
        $service = new InvoiceManageService(
            $this->invoicesRepository, 
            $this->materialRepository, 
            $this->purchaseRepository, 
            $this->entityManager);

        $invoice = new WareInvoices();
        $invoice->setAmount(0);
        $invoice->setVAT(0);

        $this->assertInstanceOf(WareInvoices::class, $service->getNewInvoice());

        $this->assertEquals($invoice, $service->getNewInvoice());
    }
}
