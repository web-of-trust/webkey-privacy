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

/**
 * X509 certificate resource
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class X509CertificateResource extends AdminResource
{
    protected static ?string $model = X509Certificate::class;
    protected static ?string $navigationGroup = 'X509';
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $slug = 'x509/certificate';

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
            'index' => Pages\ListX509Certificates::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        return static::canAccessX509();
    }
}
