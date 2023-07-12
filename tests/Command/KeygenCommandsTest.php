<?php

namespace App\Tests\Command;

use App\Application\Kernel;
use App\Tests\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class KeygenCommandsTest extends TestCase
{
    private $console;
    private $signKeyFile;
    private $verifyKeyFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->console = $this->kernel->getContainer()->get(Application::class);
        $this->signKeyFile = tempnam(sys_get_temp_dir(), 'sign');
        $this->verifyKeyFile = tempnam(sys_get_temp_dir(), 'verify');
    }

    public function testEcdsaKeygen()
    {
        $command = $this->console->find('keygen:ecdsa');
        $tester = new CommandTester($command);
        $tester->execute([
            '--sign-key-file' => $this->signKeyFile,
            '--verify-key-file' => $this->verifyKeyFile,
        ]);
        $tester->assertCommandIsSuccessful();
        $this->assertStringContainsString(
            'Ecdsa key successfully generated!', $tester->getDisplay()
        );
    }

    public function testEddsaKeygen()
    {
        $command = $this->console->find('keygen:eddsa');
        $tester = new CommandTester($command);
        $tester->execute([
            '--sign-key-file' => $this->signKeyFile,
            '--verify-key-file' => $this->verifyKeyFile,
        ]);
        $tester->assertCommandIsSuccessful();
        $this->assertStringContainsString(
            'Eddsa key successfully generated!', $tester->getDisplay()
        );
    }

    public function testHmacKeygen()
    {
        $command = $this->console->find('keygen:hmac');
        $tester = new CommandTester($command);
        $tester->execute([
            '--key-file' => $this->signKeyFile,
        ]);
        $tester->assertCommandIsSuccessful();
        $this->assertStringContainsString(
            'Hmac key successfully generated!', $tester->getDisplay()
        );
    }

    public function testRsaKeygen()
    {
        $command = $this->console->find('keygen:rsa');
        $tester = new CommandTester($command);
        $tester->execute([
            '--sign-key-file' => $this->signKeyFile,
            '--verify-key-file' => $this->verifyKeyFile,
        ]);
        $tester->assertCommandIsSuccessful();
        $this->assertStringContainsString(
            'Rsa key successfully generated!', $tester->getDisplay()
        );
    }
}
