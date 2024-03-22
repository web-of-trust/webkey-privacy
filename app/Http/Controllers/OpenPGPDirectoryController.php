<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http\Controllers;

use App\Models\OpenPGPCertificate;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenPGP\{
    Enum\ArmorType,
    Common\Armor,
    OpenPGP,
};

/**
 * OpenPGP directory controller
 *
 * @package  App
 * @category Http
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class OpenPGPDirectoryController extends Controller
{
    const EMAIL_PATTERN = '/([A-Z0-9._%+-])+@[A-Z0-9.-]+\.[A-Z]{2,}/i';

    /**
     * List all certificates.
     *
     * @return JsonResource
     */
    public function __invoke(): JsonResource
    {
        JsonResource::withoutWrapping();
        $directory = [
            'fingerprint' => [],
            'keyid' => [],
            'email' => [],
            'wkd' => [],
        ];
        $byDomains = $byEmails = [];

        $certificates = OpenPGPCertificate::with('domain')->orderBy('creation_time', 'desc')->get();
        foreach ($certificates as $cert) {
            $directory['fingerprint'][$cert->fingerprint] = $cert->key_data;
            $directory['keyid'][$cert->key_id] = $cert->key_data;
            foreach ($cert->subKeys as $subKey) {
                $directory['fingerprint'][$subKey->fingerprint] = $cert->key_data;
                $directory['keyid'][$subKey->key_id] = $cert->key_data;
            }

            $publicKey = OpenPGP::readPublicKey($cert->key_data);
            if ($email = self::extractEmail($cert->primary_user)) {
                if (empty($byEmails[$email])) {
                    $byEmails[$email] = $publicKey->getPacketList()->encode();
                }
                else {
                    $byEmails[$email] .= $publicKey->getPacketList()->encode();
                }
            }

            $domain = $cert->domain->name;
            if (empty($byDomains[$domain][$cert->wkd_hash])) {
                $byDomains[$domain][$cert->wkd_hash] = $publicKey->getPacketList()->encode();
            }
            else {
                $byDomains[$domain][$cert->wkd_hash] .= $publicKey->getPacketList()->encode();
            }
        }

        foreach ($byEmails as $email => $keyData) {
            $byEmails[$email] = Armor::encode(
                ArmorType::PublicKey, $keyData
            );
        }
        $directory['email'] = $byEmails;

        foreach ($byDomains as $domain => $byHashs) {
            foreach ($byHashs as $hash => $keyData) {
                $byDomains[$domain][$hash] = Armor::encode(
                    ArmorType::PublicKey, $keyData
                );
            }
        }
        $directory['wkd'] = $byDomains;

        return JsonResource::collection($directory);
    }

    private static function extractEmail(string $userId): string
    {
        if (preg_match(self::EMAIL_PATTERN, $userId, $matches)) {
            return $matches[0];
        };
        return '';
    }
}
