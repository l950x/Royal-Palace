<?php

namespace App\Tests\Unit;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    public function testUserValid(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $user = new User();
        $user->setNom('testUser');
        $user->setPrenom('testUser');
        $user->setEmail('testUser@gmail.com');
        $user->setPassword('testUser');
        $user->setAdresse('testUser');
        $errors = $container->get('validator')->validate($user);
        $this->assertCount(0, $errors);
        // $this->assertSame('test', $kernel->getEnvironment());
        // $routerService = static::getContainer()->get('router');
        // $myCustomService = static::getContainer()->get(CustomService::class);
    }

    public function testUserInvalid(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $user = new User();
        $user->setNom('');
        $user->setPrenom('');
        $user->setEmail('testUser@gmail.com');
        $user->setPassword('testUser');
        $user->setAdresse('testUser');

        $errors = $container->get('validator')->validate($user);
        $this->assertCount(0, $errors);
    }
}
