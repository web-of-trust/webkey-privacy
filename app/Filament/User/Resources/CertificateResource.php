<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\User\Resources;

use App\Filament\Resources\OpenPGPCertificateResource as BaseResource;

/**
 * Certificate resource
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class CertificateResource extends BaseResource
{
    protected static ?string $navigationGroup = null;
    protected static bool $shouldSkipAuthorization = true;
}
