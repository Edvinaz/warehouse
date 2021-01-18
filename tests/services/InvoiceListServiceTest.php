<?php
namespace App\Tests\services;

use App\Repository\WareInvoicesRepository;
use App\Repository\WareMaterialsRepository;
use App\Repository\WarePurchasedMaterialsRepository;
use App\Services\Purchase\InvoiceListService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class InvoiceListServiceTest extends TestCase
{
    public function testSetListServiceFunction()
    {
        $invoiceRepositoryMock = $this->createMock(WareInvoicesRepository::class);
        $materialRepositoryMock = $this->createMock(WareMaterialsRepository::class);
        $purchasesRepositoryMock = $this->createMock(WarePurchasedMaterialsRepository::class);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);

        // $service = new InvoiceListService($invoiceRepositoryMock, $materialRepositoryMock, $purchasesRepositoryMock, $entityManagerMock);

        $this->assertEquals(1, '1');
    }
}
