<?php
namespace App\Tests\services\objects;

use App\Entity\Objects\WareObjects;
use App\Entity\Sales\BuhContracts;
use App\Repository\BuhInvoicesRepository;
use App\Repository\WareObjectsRepository;
use App\Repository\BuhContractsRepository;
use App\Repository\WareWriteOffsRepository;
use App\Repository\BuhInvoiceContentRepository;
use App\Services\Objects\ObjectContractService;
use App\Repository\Objects\ObjectMaterialsRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ObjectContractServiceTest extends KernelTestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetContract()
    {
        $object = new WareObjects();
        $objectsRepository = $this
            ->getMockBuilder(WareObjectsRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock()
        ;

        $objectsRepository->method('find')->willReturn($object);
       
        $contractsRepository = $this
            ->getMockBuilder(BuhContractsRepository::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock()
        ;
        $invoicesRepository = $this
            ->getMockBuilder(BuhInvoicesRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock()
        ;
        $invoiceContentRepository = $this
            ->getMockBuilder(BuhInvoiceContentRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock()
        ;
        $objectMaterialRepository = $this
            ->getMockBuilder(ObjectMaterialsRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock()
        ;
        $writeOffRepository = $this
            ->getMockBuilder(WareWriteOffsRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock()
        ;

        $service = new ObjectContractService(
            $objectsRepository,
            $contractsRepository,
            $invoicesRepository,
            $invoiceContentRepository,
            $objectMaterialRepository,
            $writeOffRepository
        );

        $this->assertInstanceOf(BuhContracts::class, $service->getContract(3));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetExistingContract()
    {
        $object = new WareObjects();

        $contract = new BuhContracts();
        $contract->setNumber(123);
        $object->setBuhContracts($contract);

        $objectsRepository = $this
            ->getMockBuilder(WareObjectsRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock()
        ;

        $objectsRepository->method('find')->willReturn($object);
       
        $contractsRepository = $this
            ->getMockBuilder(BuhContractsRepository::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock()
        ;
        $invoicesRepository = $this
            ->getMockBuilder(BuhInvoicesRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock()
        ;
        $invoiceContentRepository = $this
            ->getMockBuilder(BuhInvoiceContentRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock()
        ;
        $objectMaterialRepository = $this
            ->getMockBuilder(ObjectMaterialsRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock()
        ;
        $writeOffRepository = $this
            ->getMockBuilder(WareWriteOffsRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock()
        ;

        $service = new ObjectContractService(
            $objectsRepository,
            $contractsRepository,
            $invoicesRepository,
            $invoiceContentRepository,
            $objectMaterialRepository,
            $writeOffRepository
        );

        $this->assertInstanceOf(BuhContracts::class, $service->getContract(3));
        $this->assertEquals(123, $service->getContract(3)->getNumber());
    }
}