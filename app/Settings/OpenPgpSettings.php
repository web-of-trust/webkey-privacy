<?php

namespace App\Settings;

use Filament\Forms\{
    Components\Select,
    Get,
};
use Illuminate\Support\Str;
use OpenPGP\Enum\{
    Ecc,
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

    private static array $keyTypeOptions = [
        KeyType::Rsa->name => 'RSA',
        KeyType::Ecc->name => 'Elliptic Curve',
        KeyType::Curve25519->name => 'Curve 25519',
        KeyType::Curve448->name => 'Curve 448',
    ];

    private static array $eccOptions = [
        Ecc::Secp256r1->name => 'NIST P-256',
        Ecc::Secp384r1->name => 'NIST P-384',
        Ecc::Secp521r1->name => 'NIST P-521',
        Ecc::BrainpoolP256r1->name => 'Brainpool P-256r1',
        Ecc::BrainpoolP384r1->name => 'Brainpool P-384r1',
        Ecc::BrainpoolP512r1->name => 'Brainpool P-512r1',
        Ecc::Ed25519->name => 'Edwards Curve 25519',
    ];

    private static array $rsaSizeOptions = [
        RSAKeySize::Normal->name => RSAKeySize::Normal->value,
        RSAKeySize::Medium->name => RSAKeySize::Medium->value,
        RSAKeySize::High->name => RSAKeySize::Medium->value,
        RSAKeySize::VeryHigh->name => RSAKeySize::Medium->value,
        RSAKeySize::UltraHigh->name => RSAKeySize::Medium->value,
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

    public function ellipticCurve(): Ecc
    {
        foreach (Ecc::cases() as $curve) {
            if ($curve->name === $this->elliptic_curve) {
                return $curve;
            }
        }
        return Ecc::Secp521r1;
    }

    public function rsaKeySize(): RSAKeySize
    {
        foreach (RSAKeySize::cases() as $keySize) {
            if ($keySize->name === $this->rsa_key_size) {
                return $keySize;
            }
        }
        return RSAKeySize::Normal;
    }
}
