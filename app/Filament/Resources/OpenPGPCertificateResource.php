<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources;

use App\Filament\Resources\OpenPGPCertificateResource\Pages;
use App\Models\OpenPGPCertificate;

/**
 * OpenPGP certificate resource
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class OpenPGPCertificateResource extends AdminResource
{
    protected static ?string $model = OpenPGPCertificate::class;
    protected static ?string $navigationGroup = 'OpenPGP';
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $slug = 'openpgp/certificate';

    public static function getModelLabel(): string
    {
        return __('Certificate');
    }

    public static function getNavigationLabel(): string
    {
        return __('Certificates');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOpenPGPCertificates::route('/'),
            'view' => Pages\ViewOpenPGPCertificate::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        return static::canAccessOpenPGP();
    }
}
