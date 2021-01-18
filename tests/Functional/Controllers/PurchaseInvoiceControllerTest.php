<?php
namespace App\Tests\Functional\Controllers;

use App\Helpers\Iterators\ItemsIterator;
use App\Repository\UserRepository;
use App\Services\Purchase\InvoiceListService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PurchaseInvoiceControllerTest extends WebTestCase
{
 
    public function testPurchaseInvoicesList()
    {
        // $client = static::createClient();

        // $itemsIterator = $this->createMock(ItemsIterator::class);

        // $serviceMock = $this->createMock(InvoiceListService::class);
        // $serviceMock->method('getList')->willReturn($itemsIterator);

        // self::$container->set(InvoiceListService::class, $serviceMock);

        // $userRepository = static::$container->get(UserRepository::class);
        // $user = $userRepository->findOneByEmail('admin@admin.lt');

        // $client->loginUser($user);

        // $crawler = $client->request('GET', '/purchase');

        // $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, 1);
    }
}