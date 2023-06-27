<?php

namespace App\Tests\Controller;

use App\Authentication\DefaultUser;
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
            new DefaultUser($username, [], ['displayName' => $displayName, 'email' => $email])
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

    public function testLoadInvalidToken()
    {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL3d3dy5leGFtcGxlLmNvbSIsImp0aSI6ImV4YW1wbGUuY29tIiwiYXVkIjoidXNlcm5hbWUiLCJpYXQiOjE2ODc4MzI1MDMuMzg4MjQxLCJleHAiOjE2ODc5MTg5MDMsInVpZCI6InVzZXJuYW1lIiwiZGlzcGxheU5hbWUiOiJkaXNwbGF5TmFtZSIsImVtYWlsIjoiZW1haWwifQ.eSW2IboGAGGimJJIrVVMkCQxx2ZWkLdoVhqxAu1YZYM';

        $app = $this->getAppInstance();
        $container = $app->getContainer();

        $repository = $container->get(TokenRepositoryInterface::class);
        $loadedToken = $repository->load($token);
        $this->assertNull($loadedToken);
    }
}
