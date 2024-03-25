<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http\Controllers;

use App\Models\OpenPGPCertificate;
use App\Support\Helper;
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
    /**
     * List webkey directory.
     *
     * @return JsonResource
     */
    public function __invoke(): JsonResource
    {
        JsonResource::withoutWrapping();

        $cacheKey = __CLASS__ . '::directory';
        $directory = cache($cacheKey);
        if (empty($directory)) {
            $directory = [
                'fingerprint' => [],
                'keyid' => [],
                'email' => [],
                'domain' => [],
            ];
            $byDomains = $byEmails = [];

            $certificates = OpenPGPCertificate::with('domain')->orderBy('creation_time', 'desc')->get();
            foreach ($certificates as $cert) {
                $publicKey = OpenPGP::readPublicKey($cert->key_data);

                $directory['fingerprint'][$cert->fingerprint] = $cert->key_data;
                $directory['keyid'][$cert->key_id] = $cert->key_data;
                foreach ($publicKey->getSubkeys() as $subKey) {
                    $directory['fingerprint'][$subKey->getFingerprint(true)] = $cert->key_data;
                    $directory['keyid'][$subKey->getKeyID(true)] = $cert->key_data;
                }

                if ($email = Helper::extractEmail($cert->primary_user)) {
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
            $directory['domain'] = $byDomains;

            cache([$cacheKey => $directory], now()->addMinutes(60));
        }
        return JsonResource::collection($directory);
    }
}
