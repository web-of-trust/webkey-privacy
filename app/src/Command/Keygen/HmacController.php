<?php declare(strict_types=1);

namespace App\Command\Keygen;

use Minicli\Command\CommandController;
use phpseclib3\Crypt\Random;

/**
 * Hmac keygen command controller class
 * 
 * @package  App
 * @category Command
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class HmacController extends CommandController
{
    private const MINIMUM_KEY_SIZE = 256;

    /**
     * {@inheritdoc}
     */
    public function handle(): void
    {
        $this->display('Hmac key generate');

        $keySize = $this->hasParam('key-size') ? (int) $this->getParam('key-size') : self::MINIMUM_KEY_SIZE;
        if ($keySize < self::MINIMUM_KEY_SIZE) {
            $message = 'Hmac key size must be at least ' . self::MINIMUM_KEY_SIZE . ' bits.';
            $this->logger->error($message);
            throw new \UnexpectedValueException($message);
        }
        file_put_contents(
            $this->getParam('key-file'),
            Random::string(($keySize + 7) >> 3)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function required(): array
    {
        return [
            'key-file',
        ];
    }
}
