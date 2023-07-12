<?php

namespace App\Tests\Controller;

use App\Authentication\LoginAuthentication;
use App\Entity\UserEntity;
use App\Tests\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticationTest extends TestCase
{
    public function testLoginAuthentication()
    {
        $username = $this->faker->username;
        $password = $this->faker->password;
        $displayName = $this->faker->name;
        $email = $this->faker->email;
        $status = $this->faker->word;
        $userEntity = new UserEntity(
            1, $username, password_hash($password, PASSWORD_DEFAULT), $displayName, $email, $status
        );

        $repositoryProphecy = $this->prophesize(ObjectRepository::class);
        $repositoryProphecy->findOneBy([
            'username' => $username,
            'status' => UserEntity::ACTIVE_STATUS,
        ])->willReturn($userEntity)->shouldBeCalledOnce();

        $managerProphecy = $this->prophesize(EntityManagerInterface::class);
        $managerProphecy->getRepository(UserEntity::class)
            ->willReturn($repositoryProphecy->reveal())
            ->shouldBeCalledOnce();

        $requestProphecy = $this->prophesize(ServerRequestInterface::class);
        $requestProphecy->getParsedBody()
            ->willReturn([
                'username' => $username,
                'password' => $password,
            ])
            ->shouldBeCalledOnce();

        $auth = new LoginAuthentication($managerProphecy->reveal());
        $user = $auth->authenticate($requestProphecy->reveal());

        $this->assertEquals($username, $user->getIdentity());
        $this->assertEquals($displayName, $user->getDetail('displayName'));
        $this->assertEquals($email, $user->getDetail('email'));
    }
}
