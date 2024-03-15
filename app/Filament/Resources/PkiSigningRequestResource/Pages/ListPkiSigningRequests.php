<?php

namespace App\Filament\Resources\PkiSigningRequestResource\Pages;

use App\Filament\Resources\PkiSigningRequestResource;
use App\Enums\KeyAlgorithmsEnum;
use App\Models\Domain;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\{
    IconColumn,
    TextColumn,
};
use Filament\Tables\Filters\{
    Filter,
    SelectFilter,
};
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListPkiSigningRequests extends ListRecords
{
    protected static string $resource = PkiSigningRequestResource::class;

    public function getTitle(): string
    {
        return __('Signing Requests');
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('domain.name')->label(__('Domain')),
            TextColumn::make('common_name')->label(__('Common Name')),
            TextColumn::make('key_algorithm')
                ->formatStateUsing(
                    static fn (int $state): string => KeyAlgorithmsEnum::tryFrom($state)?->name
                )->label(__('Key Algorithm ')),
            TextColumn::make('key_strength')
                ->suffix(' bits')->label(__('Key Strength')),
            TextColumn::make('created_at')->dateTime()->sortable()->label(__('Created At')),
        ])->filters([
            Filter::make('filter')->form([
                TextInput::make('common_name')->label(__('Common Name')),
            ])->query(
                fn (Builder $query, array $data) => $query->when(
                    $data['common_name'],
                    fn (Builder $query, string $name) => $query->where(
                        'common_name', 'like', '%' . trim($name) . '%'
                    )
                )
            ),
            SelectFilter::make('domain_id')
                ->options(
                    Domain::all()->pluck('name', 'id')
                )->label(__('Domain')),
        ])->actions([
            Action::make('export_key')->label(__('Export Key'))
                ->icon('heroicon-m-arrow-down-tray')
                ->action(function ($record) {
                    return self::getResource()::exportKey($record);
                }),
            Action::make('export_csr')->label(__('Export Csr'))
                ->icon('heroicon-m-arrow-down-tray')
                ->action(function ($record) {
                    return self::getResource()::exportCsr($record);
                }),
        ])->defaultSort('created_at', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label(__('New Signing Request')),
        ];
    }
}
