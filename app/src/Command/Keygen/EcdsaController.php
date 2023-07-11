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
    private const P_256_CURVE = 'P-256';
    private const P_384_CURVE = 'P-384';
    private const P_521_CURVE = 'P-521';

    private const CURVES = [
        self::P_256_CURVE,
        self::P_384_CURVE,
        self::P_521_CURVE,
    ];

    /**
     * {@inheritdoc}
     */
    public function handle(): void
    {
        $this->display('Ecdsa key generate');

        $curve = $this->hasParam('curve') ? $this->getParam('curve') : self::P_256_CURVE;
        if (!in_array($curve, self::CURVES)) {
            throw new \UnexpectedValueException(
                'Ecdsa curve must be one of ' . implode(', ', self::CURVES) . ' curves'
            );
        }

        $curveName = match ($curve) {
            self::P_384_CURVE => 'secp384r1',
            self::P_521_CURVE => 'secp521r1',
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
