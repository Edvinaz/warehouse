<?php
namespace App\Tests\services\purchase;

use App\Tests\Traits\ServiceTrait;
use App\Entity\Purchases\WareInvoices;
use App\Entity\Materials\WareMaterials;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\WareInvoicesRepository;
use App\Services\Purchase\InvoiceManageService;
use App\Entity\Purchases\WarePurchasedMaterials;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InvoiceManageServiceTest extends KernelTestCase
{
    use ServiceTrait;

    public function testGetNewInvoice()
    {
        $this->setPurchaseServiceRepository();
        
        $service = new InvoiceManageService(
            $this->invoicesRepository, 
            $this->materialRepository, 
            $this->purchaseRepository, 
            $this->em
        );

        $invoice = new WareInvoices();
        $invoice->setAmount(0);
        $invoice->setVAT(0);

        $this->assertInstanceOf(WareInvoices::class, $service->getNewInvoice());

        $this->assertEquals($invoice, $service->getNewInvoice());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSaveInvoice()
    {
        $invoice = new WareInvoices();
        $invoice->setNumber('123');
        $invoice->setAmount(0);
        $invoice->setVAT(0);

        $em = $this
            ->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $em->method('persist')->willReturn(true);
        $em->method('flush')->willReturn(true);

        $service = new InvoiceManageService(
            $this->entityManager->getRepository(WareInvoices::class), 
            $this->entityManager->getRepository(WareMaterials::class), 
            $this->entityManager->getRepository(WarePurchasedMaterials::class), 
            $em
        );

        $this->assertEquals('Invoice saved', $service->saveInvoice($invoice));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testDeleteInvoice()
    {
        $iID = 5;

        $invoice = new WareInvoices();
        $invoice->setNumber('123');
        $invoice->setAmount(0);
        $invoice->setVAT(0);

        $ir = $this
            ->getMockBuilder(WareInvoicesRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $ir->method('find')->willReturn($invoice);

        $em = $this
            ->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $em->method('remove')->willReturn(true);
        $em->method('flush')->willReturn(true);

        $service = new InvoiceManageService(
            $ir, 
            $this->entityManager->getRepository(WareMaterials::class), 
            $this->entityManager->getRepository(WarePurchasedMaterials::class), 
            $em
        );

        $this->assertEquals('Invoice deleted', $service->deleteInvoice($iID));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testDeleteDebitedInvoice()
    {
        $iID = 5;

        $invoice = new WareInvoices();
        $invoice->setNumber('123');
        $invoice->setAmount(0);
        $invoice->setVAT(0);

        $material = $this
            ->getMockBuilder(WarePurchasedMaterials::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $material->method('isDebited')->willReturn(true);
        $invoice->addWarePurchasedMaterial($material);

        $ir = $this
            ->getMockBuilder(WareInvoicesRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $ir->method('find')->willReturn($invoice);

        $em = $this
            ->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $em->method('remove')->willReturn(true);
        $em->method('flush')->willReturn(true);

        $service = new InvoiceManageService(
            $ir, 
            $this->entityManager->getRepository(WareMaterials::class), 
            $this->entityManager->getRepository(WarePurchasedMaterials::class), 
            $em
        );

        $this->assertEquals(
            'Cannot delete invoice', 
            $service->deleteInvoice($iID)
        );
    }
}
