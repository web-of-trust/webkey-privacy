<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http\Controllers;

use App\Http\Resources\CertificateResource;
use App\Models\Certificate;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Certificate controller
 *
 * @package  App
 * @category Http
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class CertificateController extends Controller
{
    /**
     * List all certificates.
     *
     * @return ResourceCollection
     */
    public function __invoke(): ResourceCollection
    {
        CertificateResource::withoutWrapping();
        return CertificateResource::collection(Certificate::all());
    }
}
