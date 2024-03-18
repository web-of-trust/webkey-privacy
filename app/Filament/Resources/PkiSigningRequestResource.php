<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PkiSigningRequestResource\Pages;
use App\Filament\Resources\PkiSigningRequestResource\RelationManagers;
use App\Models\PkiSigningRequest;
use Filament\Resources\Resource;

class PkiSigningRequestResource extends Resource
{
    const PASSPHRASE_STORAGE = 'pki-private';

    protected static ?string $model = PkiSigningRequest::class;
    protected static ?string $navigationGroup = 'X509';
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $slug = 'pki/signing-request';

    public static function getNavigationLabel(): string
    {
        return __('Signing Requests');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPkiSigningRequests::route('/'),
            'create' => Pages\CreatePkiSigningRequest::route('/create'),
            'view' => Pages\ViewPkiSigningRequest::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CertificatesRelationManager::class,
        ];
    }

    public static function exportKey(PkiSigningRequest $model)
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

    public static function exportCsr(PkiSigningRequest $model)
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
