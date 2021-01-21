<?php
namespace App\Tests\services\purchase;

use App\Entity\Debition\WareDebitedMaterials;
use App\Entity\Materials\WareMaterials;
use App\Entity\Purchases\WareInvoices;
use App\Entity\Purchases\WarePurchasedMaterials;
use App\Repository\WareInvoicesRepository;
use App\Repository\WareMaterialsRepository;
use App\Repository\WarePurchasedMaterialsRepository;
use App\Services\Purchase\PurchaseMaterialService;
use App\Tests\Traits\ServiceTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PurchaseMaterialServiceTest extends KernelTestCase
{
    use ServiceTrait;

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCreatePurchaseMaterialFormModel()
    {
        $this->setPurchaseServiceRepository();

        $invoice = new WareInvoices();
        $material = new WareMaterials();

        $invoicesRepository = $this
            ->getMockBuilder(WareInvoicesRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $invoicesRepository->method('find')->willReturn($invoice);
        
        $materialRepository = $this
            ->getMockBuilder(WareMaterialsRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $materialRepository->method('find')->willReturn($material);
        
        $service = new PurchaseMaterialService(
            $invoicesRepository, 
            $materialRepository, 
            $this->purchaseRepository, 
            $this->entityManager
        );

        $this->assertInstanceOf(
            WarePurchasedMaterials::class, $service->createPurchaseMaterialFormModel(1, 1)
        );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSaveNewMaterial()
    {
        $this->setPurchaseServiceRepository();

        $invoice = new WareInvoices();
        $material = new WareMaterials();

        $purchase0 = new WarePurchasedMaterials();
        $purchase0->setInvoice($invoice);
        $purchase0->setMaterial($material);
        $purchase0->setQuantity('20');
        $purchase0->setPrice('20');

        $purchase = new WarePurchasedMaterials();
        $purchase->setInvoice($invoice);
        $purchase->setMaterial($material);
        $purchase->setQuantity('2');
        $purchase->setPrice('2');

        $purchaseRepository = $this
            ->getMockBuilder(WarePurchasedMaterialsRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $purchaseRepository->method('getLastInserted')->willReturn([$purchase0]);

        $em = $this
            ->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $em->method('persist')->willReturn(true);
        $em->method('flush')->willReturn(true);
        
        $service = new PurchaseMaterialService(
            $this->invoicesRepository, 
            $this->materialRepository, 
            $purchaseRepository, 
            $em
        );

        $this->assertEquals('New material saved', $service->saveNewMaterial($purchase, 15));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSaveNewSameMaterial()
    {
        $this->setPurchaseServiceRepository();

        $invoice = new WareInvoices();
        $material = new WareMaterials();

        $purchase = new WarePurchasedMaterials();
        $purchase->setInvoice($invoice);
        $purchase->setMaterial($material);
        $purchase->setQuantity('2');
        $purchase->setPrice('2');

        $purchaseRepository = $this
            ->getMockBuilder(WarePurchasedMaterialsRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $purchaseRepository->method('getLastInserted')->willReturn([$purchase]);
        $purchaseRepository->method('find')->willReturn($purchase);

        $em = $this
            ->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $em->method('persist')->willReturn(true);
        $em->method('flush')->willReturn(true);
        
        $service = new PurchaseMaterialService(
            $this->invoicesRepository, 
            $this->materialRepository, 
            $purchaseRepository, 
            $em
        );

        $this->assertEquals('Can\'t save material', $service->saveNewMaterial($purchase, 15));

        $this->assertEquals($purchase, $service->getMaterialPurchase(5));

        $this->assertEquals('Successfully updated', $service->updatePurchasedMaterial($purchase));

        $this->assertEquals('Successfully deleted', $service->deletePurchasedMaterial(4));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCannotDeleteMaterial()
    {
        $this->setPurchaseServiceRepository();

        $invoice = new WareInvoices();
        $material = new WareMaterials();

        $debited = new WareDebitedMaterials();

        $purchase = new WarePurchasedMaterials();
        $purchase->setInvoice($invoice);
        $purchase->setMaterial($material);
        $purchase->setQuantity('2');
        $purchase->setPrice('2');
        $purchase->addWareDebitedMaterial($debited);

        $purchaseRepository = $this
            ->getMockBuilder(WarePurchasedMaterialsRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $purchaseRepository->method('getLastInserted')->willReturn([$purchase]);
        $purchaseRepository->method('find')->willReturn($purchase);

        $em = $this
            ->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $em->method('persist')->willReturn(true);
        $em->method('flush')->willReturn(true);
        
        $service = new PurchaseMaterialService(
            $this->invoicesRepository, 
            $this->materialRepository, 
            $purchaseRepository, 
            $em
        );

        $this->assertEquals('Cannot delete, because already debited', $service->deletePurchasedMaterial(4));
    }
}
