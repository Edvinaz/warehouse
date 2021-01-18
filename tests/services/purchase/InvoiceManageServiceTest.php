<?php
namespace App\Tests\services\purchase;

use PHPUnit\Framework\TestCase;
use App\Entity\Purchases\WareInvoices;
use App\Services\Analytics\PurchaseService;
use App\Services\Purchase\InvoiceManageService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InvoiceManageServiceTest extends TestCase
{
    public function testGetNewInvoice()
    {
        // $invoiceRepositoryMock = $this->createMock(WareInvoicesRepository::class);
        // $materialRepositoryMock = $this->createMock(WareMaterialsRepository::class);
        // $purchasesRepositoryMock = $this->createMock(WarePurchasedMaterialsRepository::class);
        // $entityManagerMock = $this->createMock(EntityManagerInterface::class);

        // $client = self::createClient();
        // $container = $client->getContainer();
        // $mock = $container->get('App\Services\Purchase\InvoiceManageService');

        $mm = $this->createMock(PurchaseService::class);
        $mock = new InvoiceManageService();
        $invoice = new WareInvoices();
        $invoice->setAmount(0);
        $invoice->setVAT(0);
        // $service = new InvoiceManageService();
        $this->assertEquals($mock->getNewInvoice(),$invoice);
        $this->assertInstanceOf(WareInvoices::class, $mock->getNewInvoice());
        $this->assertEquals(5,3);
    }
}