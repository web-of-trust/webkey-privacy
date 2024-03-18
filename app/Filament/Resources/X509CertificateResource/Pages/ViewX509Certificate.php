<?php

namespace App\Filament\Resources\X509CertificateResource\Pages;

use App\Filament\Resources\X509CertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewX509Certificate extends ViewRecord
{
    protected static string $resource = X509CertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
