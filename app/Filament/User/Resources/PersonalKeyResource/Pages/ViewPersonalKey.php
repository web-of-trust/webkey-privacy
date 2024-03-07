<?php

namespace App\Filament\User\Resources\PersonalKeyResource\Pages;

use App\Filament\User\Resources\PersonalKeyResource;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewPersonalKey extends ViewRecord
{
    protected static string $resource = PersonalKeyResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
