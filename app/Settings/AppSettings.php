<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Settings;

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
class AppSettings extends Settings
{
    const PASSPHRASE_REPO = 'passphrase';
    const WEBKEY_DIR = 'wkd';

    public string $passphrase_repo;
    public string $webkey_dir;
    public string $preferred_key_type;
    public string $preferred_ecc;
    public string $preferred_rsa_size;
    public string $preferred_dh_size;

    public static function group(): string
    {
        return 'app_settings';
    }

    public function passphraseRepo(): string
    {
        return $this->passphrase_repo ?: self::PASSPHRASE_REPO;
    }

    public function webkeyDir(): string
    {
        return $this->webkey_dir ?: self::WEBKEY_DIR;
    }

    public function preferredKeyType(): KeyType
    {
        foreach (KeyType::cases() as $keyType) {
            if ($keyType->name === $this->preferred_key_type) {
                return $keyType;
            }
        }
        return KeyType::Ecc;
    }

    public function preferredEcc(): CurveOid
    {
        foreach (CurveOid::cases() as $curve) {
            if ($curve->name === $this->preferred_ecc) {
                return $curve;
            }
        }
        return CurveOid::Ed25519;
    }

    public function preferredRsaSize(): RSAKeySize
    {
        foreach (RSAKeySize::cases() as $keySize) {
            if ($keySize->name === $this->preferred_rsa_size) {
                return $keySize;
            }
        }
        return RSAKeySize::S2048;
    }

    public function preferredDhSize(): DHKeySize
    {
        foreach (DHKeySize::cases() as $keySize) {
            if ($keySize->name === $this->preferred_dh_size) {
                return $keySize;
            }
        }
        return DHKeySize::L2048_N224;
    }
}
