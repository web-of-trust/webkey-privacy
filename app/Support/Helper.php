<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Support;

use App\Models\{
    X509Certificate,
    X509SigningRequest,
};
use App\Settings\AppSettings;
use OpenPGP\{
    Enum\KeyAlgorithm,
    Enum\RevocationReasonTag,
    Type\SubkeyInterface,
    OpenPGP,
};
use Symfony\Component\HttpFoundation\Response;

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
     * Generate random password from app settings.
     *
     * @return string
     */
    public static function randomPassword(): string
    {
        return app(AppSettings::class)->randomPassword();
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

    /**
     * Export X509 key.
     *
     * @param X509SigningRequest $record
     * @return Response
     */
    public static function exportX509Key(
        X509SigningRequest $record
    ): Response
    {
        $filePath = tempnam(
            sys_get_temp_dir(), $record->cn
        );
        file_put_contents($filePath, $record->key_data);
        return response()->download(
            $filePath, $record->cn . '.key', [
                'Content-Type' => 'application/pkcs8',
            ]
        )->deleteFileAfterSend(true);
    }

    /**
     * Export X509 Certificate Signing Request.
     *
     * @param X509SigningRequest $record
     * @return Response
     */
    public static function exportX509Csr(
        X509SigningRequest $record
    ): Response
    {
        $filePath = tempnam(
            sys_get_temp_dir(), $record->cn
        );
        file_put_contents($filePath, $record->csr_data);
        return response()->download(
            $filePath, $record->cn . '.csr', [
                'Content-Type' => 'application/pkcs',
            ]
        )->deleteFileAfterSend(true);
    }

    /**
     * Export X509 Certificate.
     *
     * @param X509SigningRequest $record
     * @return Response
     */
    public static function exportX509Certificate(
        X509Certificate $record
    ): Response
    {
        $filePath = tempnam(
            sys_get_temp_dir(), $record->serial_number
        );
        file_put_contents($filePath, $record->certificate_data);
        return response()->download(
            $filePath, $record->csr->cn . '.cert', [
                'Content-Type' => 'application/pkcs',
            ]
        )->deleteFileAfterSend(true);
    }

    /**
     * Export OpenPGP Key.
     *
     * @param string $name
     * @param string $keyData
     * @return Response
     */
    public static function exportOpenPGPKey(
        string $name, string $keyData
    ): Response
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
