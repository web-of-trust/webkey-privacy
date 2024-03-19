<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources;

use App\Filament\Resources\X509CertificateResource\Pages;
use App\Models\X509Certificate;
use Filament\Resources\Resource;

/**
 * View x509 signing request record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class X509CertificateResource extends Resource
{
    protected static ?string $model = X509Certificate::class;
    protected static ?string $navigationGroup = 'X509';
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $slug = 'x509/certificate';

    public static function getNavigationLabel(): string
    {
        return __('Certificates');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListX509Certificates::route('/'),
        ];
    }

    public static function exportCertificate(X509Certificate $record)
    {
        $filePath = tempnam(
            sys_get_temp_dir(), $record->serial_number
        );
        file_put_contents($filePath, $record->certificate_data);
        return response()->download(
            $filePath, $record->serial_number . '.pem', [
                'Content-Type' => 'application/pkcs8',
            ]
        )->deleteFileAfterSend(true);
    }
}
