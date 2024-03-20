<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http\Controllers;

use App\Http\Resources\OpenPGPCertificateResource;
use App\Models\OpenPGPCertificate;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * OpenPGP Certificate controller
 *
 * @package  App
 * @category Http
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class OpenPGPCertificateController extends Controller
{
    /**
     * List all certificates.
     *
     * @return JsonResource
     */
    public function __invoke(): JsonResource
    {
        JsonResource::withoutWrapping();
        return OpenPGPCertificateResource::collection(
            OpenPGPCertificate::with('domain')->orderBy('creation_time', 'desc')->get()
        );
    }
}
