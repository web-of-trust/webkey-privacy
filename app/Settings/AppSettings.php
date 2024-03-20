<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Settings;

use Filament\Forms\{
    Components\Select,
    Get,
};
use Illuminate\Support\Str;
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
    const PASSWORD_STORE  = 'key_vault';
    const PASSWORD_LENGTH = 32;

    public string $password_store;
    public int $password_length;
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
        CurveOid::Secp256k1->name => 'SECP Curve 256k1',
        CurveOid::Prime256v1->name => 'NIST Curve P-256',
        CurveOid::Secp384r1->name => 'NIST Curve P-384',
        CurveOid::Secp521r1->name => 'NIST Curve P-521',
        CurveOid::BrainpoolP256r1->name => 'Brainpool Curve P-256r1 ',
        CurveOid::BrainpoolP384r1->name => 'Brainpool Curve P-384r1 ',
        CurveOid::BrainpoolP512r1->name => 'Brainpool Curve P-512r1 ',
        CurveOid::Ed25519->name => 'Edwards Curve 25519',
    ];
    private static array $rsaSizeOptions = [
        RSAKeySize::S2048->name => '2048 bits',
        RSAKeySize::S2560->name => '2560 bits',
        RSAKeySize::S3072->name => '3072 bits',
        RSAKeySize::S3584->name => '3584 bits',
        RSAKeySize::S4096->name => '4096 bits',
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
            )->live()->required()->selectablePlaceholder(false)->label(__('Key Type')),
            Select::make('elliptic_curve')->options(self::$eccOptions)->default(
                $settings->elliptic_curve
            )->hidden(
                fn (Get $get) => $get('key_type') !== KeyType::Ecc->name
            )->required()->selectablePlaceholder(false)->label(__('Elliptic Curve')),
            Select::make('rsa_key_size')->options(self::$rsaSizeOptions)->default(
                $settings->rsa_key_size
            )->hidden(
                fn (Get $get) => $get('key_type') !== KeyType::Rsa->name
            )->required()->selectablePlaceholder(false)->label(__('RSA Key Size')),
            Select::make('dh_key_size')->options(self::$dhSizeOptions)->default(
                $settings->dh_key_size
            )->hidden(
                fn (Get $get) => $get('key_type') !== KeyType::Dsa->name
            )->required()->selectablePlaceholder(false)->label(__('DSA-ElGamal Key Size')),
        ];
    }

    public function randomPassphrase(): string
    {
        return Str::password(
            $this->passwordLength()
        );
    }

    public function passwordStore(): string
    {
        return $this->password_store ?: self::PASSWORD_STORE;
    }

    public function passwordLength(): int
    {
        return $this->password_length ?: self::PASSWORD_LENGTH;
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
