<?php

namespace App\Filament\Resources\PersonalKeyResource\Pages;

use App\Filament\Resources\PersonalKeyResource;
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
            Action::make('revoke')
                ->form([
                    TextInput::make('reason')->label(__('Revocation Reason')),
                ])
                ->visible(!$this->record->is_revoked)
                ->action(function (array $data) {
                })
                ->label(__('Revoke')),
        ];
    }
}
