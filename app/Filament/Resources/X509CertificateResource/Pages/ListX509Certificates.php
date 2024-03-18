<?php

namespace App\Filament\Resources\X509CertificateResource\Pages;

use App\Filament\Resources\X509CertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListX509Certificates extends ListRecords
{
    protected static string $resource = X509CertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
