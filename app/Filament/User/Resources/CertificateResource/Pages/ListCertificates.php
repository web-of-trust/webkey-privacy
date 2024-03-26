<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\User\Resources\CertificateResource\Pages;

use App\Filament\User\Resources\CertificateResource;
use Filament\Resources\Pages\ListRecords;

/**
 * List certificate record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class ListCertificates extends ListRecords
{
    use \App\Filament\Concerns\ListOpenPGPCertificates;

    protected static string $resource = CertificateResource::class;
}
