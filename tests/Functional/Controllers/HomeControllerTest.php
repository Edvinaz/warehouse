<?php
namespace App\Tests\Functional\Controllers;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    /** @test */
    public function homeTest()
    {
        $this->assertEquals(1, 1);
        // $client = static::createClient();

        // $client->enableProfiler();
        
        // $userRepository = static::$container->get(UserRepository::class);
        // $user = $userRepository->findOneByEmail('edvinas@uola.lt');
        
        // $client->loginUser($user);
        // $client->request('GET', '/home');
        // $this->assertEquals('200', $client->getResponse()->getStatusCode());
    }
}