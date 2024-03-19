<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources;

use App\Filament\Resources\X509SigningRequestResource\Pages;
use App\Filament\Resources\X509SigningRequestResource\RelationManagers;
use App\Models\X509SigningRequest;
use Filament\Resources\Resource;

/**
 * X509 signing request resource
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class X509SigningRequestResource extends Resource
{
    const PASSWORD_STORAGE = 'domain-x509-password';

    protected static ?string $model = X509SigningRequest::class;
    protected static ?string $navigationGroup = 'X509';
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $slug = 'x509/signing-request';

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

    public static function exportKey(X509SigningRequest $record)
    {
        $filePath = tempnam(
            sys_get_temp_dir(), $record->cn
        );
        file_put_contents($filePath, $record->key_data);
        return response()->download(
            $filePath, $record->cn . '.key', [
                'Content-Type' => 'application/pkcs8',
            ]
        )->deleteFileAfterSend(true);
    }

    public static function exportCsr(X509SigningRequest $record)
    {
        $filePath = tempnam(
            sys_get_temp_dir(), $record->cn
        );
        file_put_contents($filePath, $record->csr_data);
        return response()->download(
            $filePath, $record->cn . '.csr', [
                'Content-Type' => 'application/pkcs8',
            ]
        )->deleteFileAfterSend(true);
    }
}
