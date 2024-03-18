<?php

namespace App\Filament\Resources;

use App\Filament\Resources\X509SigningRequestResource\Pages;
use App\Filament\Resources\X509SigningRequestResource\RelationManagers;
use App\Models\X509SigningRequest;
use Filament\Resources\Resource;

class X509SigningRequestResource extends Resource
{
    const PASSPHRASE_STORAGE = 'x509-private';

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

    public static function exportKey(X509SigningRequest $model)
    {
        $filePath = tempnam(
            sys_get_temp_dir(), $model->common_name
        );
        file_put_contents($filePath, $model->key_data);
        return response()->download(
            $filePath, $model->common_name . '.key', [
                'Content-Type' => 'application/pkcs8',
            ]
        )->deleteFileAfterSend(true);
    }

    public static function exportCsr(X509SigningRequest $model)
    {
        $filePath = tempnam(
            sys_get_temp_dir(), $model->common_name
        );
        file_put_contents($filePath, $model->csr_data);
        return response()->download(
            $filePath, $model->common_name . '.csr', [
                'Content-Type' => 'application/pkcs8',
            ]
        )->deleteFileAfterSend(true);
    }
}
