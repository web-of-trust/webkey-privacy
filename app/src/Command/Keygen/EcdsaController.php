<?php declare(strict_types=1);

namespace App\Command\Keygen;

use App\Command\KeygenController;
use phpseclib3\Crypt\EC;

/**
 * Ecdsa keygen command controller class
 * 
 * @package  App
 * @category Command
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class EcdsaController extends KeygenController
{
    private const DEFAULT_CURVE = 'P-256';
    private const CURVES = [
        'P-256',
        'P-384',
        'P-521',
    ];

    /**
     * {@inheritdoc}
     */
    public function handle(): void
    {
        $this->display('Ecdsa key generate');

        $curve = $this->hasParam('curve') ? $this->getParam('curve') : self::DEFAULT_CURVE;
        if (!in_array($curve, self::CURVES)) {
            $message = 'Ecdsa curve must be P-256, P-384 or P-521';
            $this->logger->error($message);
            throw new \UnexpectedValueException($message);
        }

        $curveName = match ($curve) {
            'P-384' => 'secp384r1',
            'P-521' => 'secp521r1',
            default => 'secp256r1',
        };
        $ecKey = EC::createKey($curveName);
        file_put_contents(
            $this->getParam('sign-key-file'),
            $ecKey->toString('PKCS8')
        );
        file_put_contents(
            $this->getParam('verify-key-file'),
            $ecKey->getPublicKey()->toString('PKCS8')
        );
    }
}
