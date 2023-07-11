<?php

namespace App\Tests\Controller;

use App\Authentication\AuthenticatedUser;
use App\Authentication\TokenRepositoryInterface;
use App\Tests\TestCase;

class JwtTokenTest extends TestCase
{
    public function testCreateLoadJwtToken()
    {
        $username = $this->faker->username;
        $displayName = $this->faker->name;
        $email = $this->faker->email;

        $app = $this->getAppInstance();
        $container = $app->getContainer();

        $repository = $container->get(TokenRepositoryInterface::class);
        $token = $repository->create(
            new AuthenticatedUser($username, [], ['displayName' => $displayName, 'email' => $email])
        );

        $now = new \DateTimeImmutable();
        $this->assertEquals(
            $token->expiresAt()->getTimestamp(),
            $now->getTimestamp() + (int) $container->get('jwt.expires')
        );

        $payload = $token->getPayload();
        $this->assertEquals($username, $payload['uid']);
        $this->assertEquals($displayName, $payload['displayName']);
        $this->assertEquals($email, $payload['email']);

        $loadedToken = $repository->load($token->getToken());
        $payload = $loadedToken->getPayload();
        $this->assertEquals($username, $payload['uid']);
        $this->assertEquals($displayName, $payload['displayName']);
        $this->assertEquals($email, $payload['email']);
    }
}
