<?php declare(strict_types=1);

namespace App\Command\Keygen;

use Minicli\Command\CommandController;
use Minicli\Exception\MissingParametersException;
use phpseclib3\Crypt\RSA;

/**
 * Rsa keygen command controller class
 * 
 * @package  App
 * @category Command
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class RsaController extends CommandController
{
    private const MINIMUM_KEY_SIZE = 2048;

    /**
     * {@inheritdoc}
     */
    public function handle(): void
    {
        $keySize = $this->hasParam('key-size') ? (int) $this->getParam('key-size') : self::MINIMUM_KEY_SIZE;
        if ($keySize < self::MINIMUM_KEY_SIZE) {
            throw new \UnexpectedValueException(
                'Rsa key size must be at least ' . self::MINIMUM_KEY_SIZE . ' bits.'
            );
        }

        if (!$this->hasParam('sign-key-file') || !$this->hasParam('verify-key-file')) {
            throw new MissingParametersException([
                'sign-key-file',
                'verify-key-file',
            ]);
        }
        else {
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
}
