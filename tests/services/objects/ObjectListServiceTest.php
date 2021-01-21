<?php
namespace App\Tests\services\objects;

use App\Entity\Contrahents;
use App\Entity\Objects\WareObjects;
use App\Repository\BuhContractsRepository;
use App\Repository\BuhInvoiceContentRepository;
use App\Repository\BuhInvoicesRepository;
use App\Repository\Objects\ObjectMaterialsRepository;
use App\Repository\WareObjectsRepository;
use App\Repository\WareWriteOffsRepository;
use App\Services\Objects\ObjectListService;
use App\Tests\Traits\ServiceTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ObjectListServiceTest extends KernelTestCase
{
    use ServiceTrait;

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider objectList
     */
    public function testGetObjectList($search, $provide, $result)
    {
        $objectsRepository = $this
            ->getMockBuilder(WareObjectsRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock()
        ;
        $objectsRepository->method('getObjectsList')->willReturn($provide);

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

        $service = new ObjectListService(
            $objectsRepository,
            $contractsRepository,
            $invoicesRepository,
            $invoiceContentRepository,
            $objectMaterialRepository,
            $writeOffRepository
        );

        $this->assertEquals($result, $service->getObjectList($search)->current());
    }

    public function objectList(): array
    {
        $o1 = new WareObjects();
        $o1->setAdress('Jonavos g. 12, Kaunas');
        $o1->setName('Vidaus elektros tinklai');

        $c1 = new Contrahents();
        $c1->setName('Maxima');
        $o1->setContrahent($c1);

        $o2 = new WareObjects();
        $o2->setAdress('Utenos g. 14, Kaunas');
        $o2->setName('Elektros jėgos tinklai');

        $c2 = new Contrahents();
        $c2->setName('Ermitažas');
        $o2->setContrahent($c2);

        $o3 = new WareObjects();
        $o3->setAdress('Naujoji g. 20, Kaunas');
        $o3->setName('Lempų keitimas');

        $c3 = new Contrahents();
        $c3->setName('Senukai');
        $o3->setContrahent($c3);

        $o4 = new WareObjects();
        $o4->setAdress('Pienių g. 6, Kaunas');
        $o4->setName('Laiptinės apšvietimo remontas');
        $o4->setContrahent($c1);

        $o5 = new WareObjects();
        $o5->setAdress('Ramunių g. 3, Kaunas');
        $o5->setName('Fasado apšvietimo aptarnavimas');
        $o5->setContrahent($c2);

        $provider = [$o1, $o2, $o3, $o4, $o5];

        /**
         * 1: search string, 2: provided array, 3: result array
         */
        return [
            ['', [], []],
            ['', $provider, $provider],
            ['Kaunas', $provider, $provider],
            ['aptar', $provider, [$o5]],
            ['tinklai', $provider, [$o1, $o2]],
            ['ermi', $provider, [$o2, $o5]],
        ];
    }
}