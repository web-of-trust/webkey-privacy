<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * OpenPGP certificate resource
 *
 * @package  App
 * @category Http
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class OpenPGPCertificateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'domain'          => $this->domain->name,
            'primary_user'    => $this->primary_user,
            'wkd_hash'        => $this->wkd_hash,
            'fingerprint'     => $this->fingerprint,
            'key_id'          => $this->key_id,
            'key_algorithm'   => $this->key_algorithm,
            'key_strength'    => $this->key_strength,
            'key_version'     => $this->key_version,
            'creation_time'   => $this->creation_time,
            'expiration_time' => $this->expiration_time,
            'key_data'        => $this->key_data,
        ];
    }
}
