<?php

namespace App\Tests\services\purchase;

use App\Entity\Contrahents;
use App\Entity\Purchases\WareInvoices;
use App\Tests\Traits\ServiceTrait;
use App\Repository\WareInvoicesRepository;
use App\Services\Purchase\InvoiceListService;
use SebastianBergmann\RecursionContext\Context;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InvoiceListServiceTest extends KernelTestCase
{
    use ServiceTrait;
    
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetEmptyList()
    {
        $this->setPurchaseServiceRepository();  
        
        $invoiceRepository = $this
            ->getMockBuilder(WareInvoicesRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $invoiceRepository->method('invoiceList')->willReturn([]);
        
        $service = new InvoiceListService(
            $invoiceRepository, 
            $this->materialRepository, 
            $this->purchaseRepository, 
            $this->em
        );

        $list = [];

        $this->assertEquals($list, $service->getList()->current());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetSearchList()
    {
        $this->setPurchaseServiceRepository();  

        $contrahent = new Contrahents();
        $contrahent->setName('Contrahent');

        $contrahent2 = new Contrahents();
        $contrahent2->setName('Seller');

        $invoice = new WareInvoices();
        $invoice->setContrahent($contrahent);
        $invoice->setNumber('WARE 00123');

        $invoice2 = new WareInvoices();
        $invoice2->setContrahent($contrahent2);
        $invoice2->setNumber('SL 3566');

        $this->assertEquals('Contrahent', $invoice->getContrahent()->getName());
        $this->assertEquals('Seller', $invoice2->getContrahent()->getName());
        
        $invoiceRepository = $this
            ->getMockBuilder(WareInvoicesRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $invoiceRepository->method('invoiceList')
            ->willReturn([
                $invoice, 
                $invoice2
            ]);
        
        $service = new InvoiceListService(
            $invoiceRepository, 
            $this->materialRepository, 
            $this->purchaseRepository, 
            $this->em
        );

        $list = [$invoice];
        $result = $service->getList('contr');
        $this->assertEquals($list, $result->current());
        
        $result = $service->getList('123');
        $this->assertEquals($list, $result->current());

    }
}