<?php

namespace App\Filament\Resources\PkiCertificateResource\Pages;

use App\Filament\Resources\PkiCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPkiCertificates extends ListRecords
{
    protected static string $resource = PkiCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
