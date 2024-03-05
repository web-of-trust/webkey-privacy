<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources;

use App\Filament\Resources\CertificateResource\Pages;
use App\Models\Certificate;
use Filament\Resources\Resource;

/**
 * Certificate resource
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $slug = 'certificate';

    public static function getNavigationLabel(): string
    {
        return __('Certificate Manager');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificates::route('/'),
            'view' => Pages\ViewCertificate::route('/{record}'),
        ];
    }
}
