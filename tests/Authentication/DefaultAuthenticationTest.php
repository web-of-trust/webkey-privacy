<?php

namespace App\Tests\Controller;

use App\Authentication\DefaultAuthentication;
use App\Entity\UserEntity;
use App\Tests\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Psr\Http\Message\ServerRequestInterface;

class DefaultAuthenticationTest extends TestCase
{
    public function testDefaultAuthentication()
    {
        $userEntity = new UserEntity(
            1, 'username', password_hash('password', PASSWORD_DEFAULT), 'displayName', 'email', 'status'
        );

        $repositoryProphecy = $this->prophesize(ObjectRepository::class);
        $repositoryProphecy->findOneBy(['username' => 'username'])
            ->willReturn($userEntity)
            ->shouldBeCalledOnce();

        $managerProphecy = $this->prophesize(EntityManagerInterface::class);
        $managerProphecy->getRepository(UserEntity::class)
            ->willReturn($repositoryProphecy->reveal())
            ->shouldBeCalledOnce();

        $requestProphecy = $this->prophesize(ServerRequestInterface::class);
        $requestProphecy->getParsedBody()
            ->willReturn([
                'username' => 'username',
                'password' => 'password',
            ])
            ->shouldBeCalledOnce();

        $auth = new DefaultAuthentication($managerProphecy->reveal());
        $user = $auth->authenticate($requestProphecy->reveal());

        $this->assertEquals('username', $user->getIdentity());
        $this->assertEquals('displayName', $user->getDetail('displayName'));
        $this->assertEquals('email', $user->getDetail('email'));
    }
}
