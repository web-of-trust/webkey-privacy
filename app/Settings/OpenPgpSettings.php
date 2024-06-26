<?php

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
    HashAlgorithm,
    RSAKeySize,
    SymmetricAlgorithm,
};
use Spatie\LaravelSettings\Settings;

class OpenPgpSettings extends Settings
{
    const PASSWORD_STORE  = 'password_vault';
    const PASSWORD_LENGTH = 32;

    public string $password_store;
    public int $password_length;
    public string $preferred_hash;
    public string $preferred_symmetric;
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

    public static function keySettings(): array
    {
        $settings = app(self::class);
        return [
            Select::make('key_type')->options(self::$keyTypeOptions)->default(
                $settings->key_type
            )->live()->required()->label(__('Key Type')),
            Select::make('elliptic_curve')->options(self::$eccOptions)->default(
                $settings->elliptic_curve
            )->hidden(
                fn (Get $get) => $get('key_type') !== KeyType::Ecc->name
            )->required()->label(__('Elliptic Curve')),
            Select::make('rsa_key_size')->options(self::$rsaSizeOptions)->default(
                $settings->rsa_key_size
            )->hidden(
                fn (Get $get) => $get('key_type') !== KeyType::Rsa->name
            )->required()->label(__('RSA Key Size')),
            Select::make('dh_key_size')->options(self::$dhSizeOptions)->default(
                $settings->dh_key_size
            )->hidden(
                fn (Get $get) => $get('key_type') !== KeyType::Dsa->name
            )->required()->label(__('DSA-ElGamal Key Size')),
        ];
    }

    public static function group(): string
    {
        return 'openpgp';
    }

    public function randomPassword(): string
    {
        return Str::password(
            $this->passwordLength()
        );
    }

    public function passwordStore(): string
    {
        return empty($this->password_store) ?
               self::PASSWORD_STORE : $this->password_store;
    }

    public function passwordLength(): int
    {
        return $this->password_length < self::PASSWORD_LENGTH ?
               self::PASSWORD_LENGTH : $this->password_length;
    }

    public function preferredHash(): HashAlgorithm
    {
        foreach (HashAlgorithm::cases() as $algo) {
            if ($algo->name === $this->preferred_hash) {
                return $algo;
            }
        }
        return HashAlgorithm::Sha256;
    }

    public function preferredSymmetric(): SymmetricAlgorithm
    {
        foreach (SymmetricAlgorithm::cases() as $algo) {
            if ($algo->name === $this->preferred_symmetric) {
                return $algo;
            }
        }
        return SymmetricAlgorithm::Aes256;
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
        return CurveOid::Secp521r1;
    }

    public function rsaKeySize(): RSAKeySize
    {
        foreach (RSAKeySize::cases() as $keySize) {
            if ($keySize->name === $this->rsa_key_size) {
                return $keySize;
            }
        }
        return RSAKeySize::S2048;
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
