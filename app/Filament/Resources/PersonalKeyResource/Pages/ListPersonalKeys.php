<?php

namespace App\Filament\Resources\PersonalKeyResource\Pages;

use App\Filament\Resources\CertificateResource;
use App\Filament\Resources\PersonalKeyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\{
    IconColumn,
    TextColumn,
};
use Filament\Tables\Table;

class ListPersonalKeys extends ListRecords
{
    protected static string $resource = PersonalKeyResource::class;

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('certificate.primary_user')->label(__('User ID')),
            TextColumn::make('certificate.key_id')
                ->formatStateUsing(
                    static fn (string $state): string => strtoupper($state)
                )->label(__('Key ID')),
            TextColumn::make('certificate.key_algorithm')
                ->formatStateUsing(
                    static fn (int $state): string => CertificateResource::keyAlgorithm($state)
                )->label(__('Key Algorithm ')),
            TextColumn::make('certificate.key_strength')
                ->suffix(' bits')->label(__('Key Strength')),
            IconColumn::make('is_revoked')
                ->icon(fn (bool $state): string => match ($state) {
                    false => 'heroicon-o-x-circle',
                    true => 'heroicon-o-check-circle',
                })->color(fn (bool $state): string => match ($state) {
                    false => 'success',
                    true => 'danger',
                })->label(__('Is Revoked')),
            TextColumn::make('certificate.creation_time')
                ->sortable()->label(__('Creation Time')),
        ])->emptyStateHeading(
            __('No personal key yet')
        );
    }
}
