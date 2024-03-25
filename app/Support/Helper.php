<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Support;

use OpenPGP\{
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

    public static function extractEmail(string $userId): string
    {
        if (preg_match(self::EMAIL_PATTERN, $userId, $matches)) {
            return $matches[0];
        };
        return '';
    }

    public static function getSubkeys(string $keyData): array
    {
        $subKeys = [];
        $publicKey = OpenPGP::readPublicKey($keyData);
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
}
