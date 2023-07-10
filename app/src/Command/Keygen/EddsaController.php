<?php declare(strict_types=1);

namespace App\Command\Keygen;

use App\Command\KeygenController;
use phpseclib3\Crypt\EC;

/**
 * Eddsa keygen command controller class
 * 
 * @package  App
 * @category Command
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class EddsaController extends KeygenController
{
    private const CURVE_NAME = 'Ed25519';

    /**
     * {@inheritdoc}
     */
    public function handle(): void
    {
        $edKey = EC::createKey(self::CURVE_NAME);
        file_put_contents(
            $this->getParam('sign-key-file'),
            $edKey->toString('libsodium')
        );
        file_put_contents(
            $this->getParam('verify-key-file'),
            $edKey->getPublicKey()->toString('libsodium')
        );
    }
}
