<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\OpenPGPCertificateResource\Pages;

use App\Filament\Resources\OpenPGPCertificateResource;
use Filament\Resources\Pages\ViewRecord;

/**
 * View OpenPGP certificate record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class ViewOpenPGPCertificate extends ViewRecord
{
    use \App\Filament\Concerns\ViewOpenPGPCertificate;

    protected static string $resource = OpenPGPCertificateResource::class;
}
