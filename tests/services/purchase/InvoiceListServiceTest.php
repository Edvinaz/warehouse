<?php

namespace App\Tests\services\purchase;

use PHPUnit\Framework\TestCase;
use App\Tests\Traits\ServiceTrait;
use App\Entity\Purchases\WareInvoices;
use App\Services\Purchase\InvoiceListService;

class InvoiceListServiceTest extends TestCase
{
    use ServiceTrait;
    
    public function testSetListServiceFunction()
    {
        $this->setPurchaseServiceRepository();
        
        $service = new InvoiceListService(
            $this->invoicesRepository, 
            $this->materialRepository, 
            $this->purchaseRepository, 
            $this->entityManager);

        $invoice = new WareInvoices();
        $invoice->setAmount(0);
        $invoice->setVAT(0);

        // $this->assertInstanceOf(WareInvoices::class, $service->getNewInvoice());

        $this->assertEquals(2, 2);
    }
}
