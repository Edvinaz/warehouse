<?php
namespace App\Tests\Traits;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\WareInvoicesRepository;
use App\Repository\WareMaterialsRepository;
use App\Repository\WarePurchasedMaterialsRepository;

trait ServiceTrait
{
    private $invoicesRepository;
    private $materialRepository;
    private $purchaseRepository;
    private $em;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private function setPurchaseServiceRepository()
    {
        $this->invoicesRepository = $this
            ->getMockBuilder(WareInvoicesRepository::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $this->materialRepository = $this
            ->getMockBuilder(WareMaterialsRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $this->purchaseRepository = $this
            ->getMockBuilder(WarePurchasedMaterialsRepository::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $this->em = $this
            ->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    } 

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
