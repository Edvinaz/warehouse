<?php
namespace App\Tests\services\purchase;

use App\Models\PurchaseInvoice;
use App\Tests\Traits\ServiceTrait;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\Purchase\InvoiceDetailsService;
use App\Repository\WarePurchasedMaterialsRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InvoiceDetailsServiceTest extends KernelTestCase
{
    use ServiceTrait;

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetPurchaseInvoice()
    {
        $this->setPurchaseServiceRepository();

        $purchaseRepository = $this
            ->getMockBuilder(WarePurchasedMaterialsRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $purchaseRepository->method('getLastInserted')->willReturn('');

        $em = $this
            ->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $em->method('persist')->willReturn(true);
        $em->method('flush')->willReturn(true);
        
        $service = new InvoiceDetailsService(
            $this->invoicesRepository, 
            $this->materialRepository, 
            $purchaseRepository, 
            $em
        );

        $this->assertInstanceOf(PurchaseInvoice::class, $service->getPurchaseInvoice());
    }
}