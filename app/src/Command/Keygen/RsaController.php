<?php declare(strict_types=1);

namespace App\Command\Keygen;

use App\Command\KeygenController;
use phpseclib3\Crypt\RSA;

/**
 * Rsa keygen command controller class
 * 
 * @package  App
 * @category Command
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class RsaController extends KeygenController
{
    private const MINIMUM_KEY_SIZE = 2048;

    /**
     * {@inheritdoc}
     */
    public function handle(): void
    {
        $this->display('Rsa key generate');

        $keySize = $this->hasParam('key-size') ? (int) $this->getParam('key-size') : self::MINIMUM_KEY_SIZE;
        if ($keySize < self::MINIMUM_KEY_SIZE) {
            $message = 'Rsa key size must be at least ' . self::MINIMUM_KEY_SIZE . ' bits.';
            $this->logger->error($message);
            throw new \UnexpectedValueException($message);
        }

        $rsaKey = RSA::createKey($keySize);
        file_put_contents(
            $this->getParam('sign-key-file'),
            $rsaKey->toString('PKCS8')
        );
        file_put_contents(
            $this->getParam('verify-key-file'),
            $rsaKey->getPublicKey()->toString('PKCS8')
        );
    }
}
