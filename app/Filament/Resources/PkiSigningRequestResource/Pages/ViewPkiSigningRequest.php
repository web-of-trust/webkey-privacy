<?php

namespace App\Filament\Resources\PkiSigningRequestResource\Pages;

use App\Filament\Resources\PkiSigningRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPkiSigningRequest extends ViewRecord
{
    protected static string $resource = PkiSigningRequestResource::class;

    public function getTitle(): string
    {
        return __('View Signing Request');
    }

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
