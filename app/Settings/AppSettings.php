<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Settings;

use Filament\Forms\Components\Select;
use OpenPGP\Enum\{
    CurveOid,
    DHKeySize,
    KeyType,
    RSAKeySize,
};
use Spatie\LaravelSettings\Settings;

/**
 * App settings
 *
 * @package  App
 * @category Settings
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
final class AppSettings extends Settings
{
    const PASSPHRASE_STORE  = 'key_vault';
    const PASSPHRASE_LENGTH = 32;

    public string $passphrase_store;
    public int $passphrase_length;
    public string $key_type;
    public string $elliptic_curve;
    public string $rsa_key_size;
    public string $dh_key_size;

    private static array $keyTypeOptions = [
        KeyType::Rsa->name => 'RSA',
        KeyType::Dsa->name => 'DSA ElGamal',
        KeyType::Ecc->name => 'Elliptic Curve',
    ];
    private static array $eccOptions = [
        CurveOid::Secp256k1->name => 'SECP 256k1 Curve',
        CurveOid::Prime256v1->name => 'NIST P-256 Curve',
        CurveOid::Secp384r1->name => 'NIST P-384 Curve',
        CurveOid::Secp521r1->name => 'NIST P-521 Curve',
        CurveOid::BrainpoolP256r1->name => 'BrainpoolP256r1 Curve',
        CurveOid::BrainpoolP384r1->name => 'BrainpoolP384r1 Curve',
        CurveOid::BrainpoolP512r1->name => 'BrainpoolP512r1 Curve',
        CurveOid::Ed25519->name => 'Curve 25519',
    ];
    private static array $rsaSizeOptions = [
        RSAKeySize::S2048->name => RSAKeySize::S2048->value . ' bits',
        RSAKeySize::S2560->name => RSAKeySize::S2560->value . ' bits',
        RSAKeySize::S3072->name => RSAKeySize::S3072->value . ' bits',
        RSAKeySize::S3584->name => RSAKeySize::S3584->value . ' bits',
        RSAKeySize::S4096->name => RSAKeySize::S4096->value . ' bits',
    ];
    private static array $dhSizeOptions = [
        DHKeySize::L1024_N160->name => '1024 bits',
        DHKeySize::L2048_N224->name => '2048 bits (224)',
        DHKeySize::L2048_N256->name => '2048 bits (256)',
        DHKeySize::L3072_N256->name => '3072 bits',
    ];

    public static function group(): string
    {
        return 'app_settings';
    }

    public static function keySettings(): array
    {
        $settings = app(self::class);
        return [
            Select::make('key_type')->options(self::$keyTypeOptions)->default(
                $settings->key_type
            )->selectablePlaceholder(false)->label(__('Key Type')),
            Select::make('elliptic_curve')->options(self::$eccOptions)->default(
                $settings->elliptic_curve
            )->selectablePlaceholder(false)->label(__('Elliptic Curve')),
            Select::make('rsa_key_size')->options(self::$rsaSizeOptions)->default(
                $settings->rsa_key_size
            )->selectablePlaceholder(false)->label(__('RSA Key Size')),
            Select::make('dh_key_size')->options(self::$dhSizeOptions)->default(
                $settings->dh_key_size
            )->selectablePlaceholder(false)->label(__('DSA-ElGamal Key Size')),
        ];
    }

    public function passphraseStore(): string
    {
        return $this->passphrase_store ?: self::PASSPHRASE_STORE;
    }

    public function passphraseLength(): int
    {
        return $this->passphrase_length ?: self::PASSPHASE_LENGTH;
    }

    public function keyType(): KeyType
    {
        foreach (KeyType::cases() as $keyType) {
            if ($keyType->name === $this->key_type) {
                return $keyType;
            }
        }
        return KeyType::Ecc;
    }

    public function ellipticCurve(): CurveOid
    {
        foreach (CurveOid::cases() as $curve) {
            if ($curve->name === $this->elliptic_curve) {
                return $curve;
            }
        }
        return CurveOid::Ed25519;
    }

    public function rsaKeySize(): RSAKeySize
    {
        foreach (RSAKeySize::cases() as $keySize) {
            if ($keySize->name === $this->rsa_key_size) {
                return $keySize;
            }
        }
        return RSAKeySize::S3072;
    }

    public function dhKeySize(): DHKeySize
    {
        foreach (DHKeySize::cases() as $keySize) {
            if ($keySize->name === $this->dh_key_size) {
                return $keySize;
            }
        }
        return DHKeySize::L2048_N224;
    }
}
