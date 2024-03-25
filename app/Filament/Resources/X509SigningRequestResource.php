<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources;

use App\Enums\Role;
use App\Filament\Resources\X509SigningRequestResource\Pages;
use App\Filament\Resources\X509SigningRequestResource\RelationManagers;
use App\Models\X509SigningRequest;

/**
 * X509 signing request resource
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class X509SigningRequestResource extends AdminResource
{
    const PASSWORD_STORAGE = 'domain-x509-password';

    protected static ?string $model = X509SigningRequest::class;
    protected static ?string $navigationGroup = 'X509';
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $slug = 'x509/signing-request';

    public static function getModelLabel(): string
    {
        return __('Signing Request');
    }

    public static function getNavigationLabel(): string
    {
        return __('Signing Requests');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListX509SigningRequests::route('/'),
            'create' => Pages\CreateX509SigningRequest::route('/create'),
            'view' => Pages\ViewX509SigningRequest::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CertificatesRelationManager::class,
        ];
    }

    public static function canAccess(): bool
    {
        return static::canAccessX509();
    }
}
