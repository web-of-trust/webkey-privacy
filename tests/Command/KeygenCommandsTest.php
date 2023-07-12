<?php

namespace App\Tests\Command;

use App\Application\Kernel;
use App\Tests\TestCase;

class KeygenCommandsTest extends TestCase
{
    private $signKeyFile;
    private $verifyKeyFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->signKeyFile = tempnam(sys_get_temp_dir(), 'sign');
        $this->verifyKeyFile = tempnam(sys_get_temp_dir(), 'verify');
    }

    public function testEcdsaKeygen()
    {
        $this->expectOutputRegex('/Ecdsa key generate/');
        $this->kernel->runCommand([
            'webkey',
            'keygen',
            'ecdsa',
            'sign-key-file=' . $this->signKeyFile,
            'verify-key-file=' . $this->verifyKeyFile,
        ]);
    }

    public function testEddsaKeygen()
    {
        $this->expectOutputRegex('/Eddsa key generate/');
        $this->kernel->runCommand([
            'webkey',
            'keygen',
            'eddsa',
            'sign-key-file=' . $this->signKeyFile,
            'verify-key-file=' . $this->verifyKeyFile,
        ]);
    }

    public function testHmacKeygen()
    {
        $this->expectOutputRegex('/Hmac key generate/');
        $this->kernel->runCommand([
            'webkey',
            'keygen',
            'hmac',
            'key-file=' . $this->signKeyFile,
        ]);
    }

    public function testRsaKeygen()
    {
        $this->expectOutputRegex('/Rsa key generate/');
        $this->kernel->runCommand([
            'webkey',
            'keygen',
            'rsa',
            'sign-key-file=' . $this->signKeyFile,
            'verify-key-file=' . $this->verifyKeyFile,
        ]);
    }
}
