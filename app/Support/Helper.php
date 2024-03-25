<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Support;

use App\Models\OpenPGPCertificate;
use OpenPGP\{
    Enum\KeyAlgorithm,
    Enum\RevocationReasonTag,
    Type\SubkeyInterface,
    OpenPGP,
};

/**
 * Helper class
 *
 * @package  App
 * @category Support
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
final class Helper
{
    const EMAIL_PATTERN = '/([A-Z0-9._%+-])+@[A-Z0-9.-]+\.[A-Z]{2,}/i';

    /**
     * Extract email address from string.
     *
     * @param string $subject
     * @return string
     */
    public static function extractEmail(string $subject): string
    {
        if (preg_match(self::EMAIL_PATTERN, $subject, $matches)) {
            return $matches[0];
        };
        return '';
    }

    /**
     * Get sub keys from armored OpenPGP public key.
     *
     * @param string $armoredPublicKey
     * @return array
     */
    public static function getSubkeys(string $armoredPublicKey): array
    {
        $subKeys = [];
        $publicKey = OpenPGP::readPublicKey($armoredPublicKey);
        foreach ($publicKey->getSubkeys() as $subKey) {
            $subKeys[] = new class ($subKey) {
                function __construct(SubkeyInterface $subKey) {
                    $this->fingerprint = $subKey->getFingerprint(true);
                    $this->key_id = $subKey->getKeyID(true);
                    $this->key_algorithm = $subKey->getKeyAlgorithm()->name;
                    $this->key_strength = $subKey->getKeyStrength();
                    $this->creation_time = $subKey->getCreationTime();
                    $this->expiration_time = $subKey->getExpirationTime();
                }
            };
        }
        return $subKeys;
    }

    public static function exportOpenPGPKey(string $name, string $keyData)
    {
        $filePath = tempnam(
            sys_get_temp_dir(), $name
        );
        file_put_contents($filePath, $keyData);
        return response()->download(
            $filePath, $name . '.asc', [
                'Content-Type' => 'application/pgp-keys',
            ]
        )->deleteFileAfterSend(true);
    }

    public static function exportOpenPGPCert(OpenPGPCertificate $model)
    {
        $filePath = tempnam(
            sys_get_temp_dir(), $model->key_id
        );
        file_put_contents($filePath, $model->key_data);
        return response()->download(
            $filePath, $model->key_id . '.asc', [
                'Content-Type' => 'application/pgp-keys',
            ]
        )->deleteFileAfterSend(true);
    }

    public static function keyAlgorithm(int $algo): string
    {
        return KeyAlgorithm::tryFrom($algo)?->name ?? '';
    }

    public static function revocationReason(int $tag): string
    {
        return match (RevocationReasonTag::tryFrom($tag)) {
            RevocationReasonTag::NoReason => __('No reason'),
            RevocationReasonTag::KeySuperseded => __('Key is superseded'),
            RevocationReasonTag::KeyCompromised => __('Key has been compromised'),
            RevocationReasonTag::KeyRetired => __('Key is retired'),
            RevocationReasonTag::UserIDInvalid => __('User ID is invalid'),
            default => '',
        };
    }
}
