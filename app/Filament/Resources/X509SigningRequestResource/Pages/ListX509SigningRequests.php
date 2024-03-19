<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\X509SigningRequestResource\Pages;

use App\Filament\Resources\X509SigningRequestResource;
use App\Enums\KeyAlgorithmsEnum;
use App\Models\Domain;
use Filament\Actions\CreateAction;
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

/**
 * List x509 signing request record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class ListX509SigningRequests extends ListRecords
{
    protected static string $resource = X509SigningRequestResource::class;

    public function getTitle(): string
    {
        return __('Signing Requests');
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('domain.name')->label(__('Domain')),
            TextColumn::make('cn')->label(__('Common Name')),
            TextColumn::make('key_algorithm')
                ->formatStateUsing(
                    static fn (int $state): string => KeyAlgorithmsEnum::tryFrom($state)?->name
                )->label(__('Key Algorithm ')),
            TextColumn::make('key_strength')
                ->suffix(' bits')->label(__('Key Strength')),
            TextColumn::make('created_at')->dateTime()->sortable()->label(__('Created At')),
        ])->filters([
            Filter::make('filter')->form([
                TextInput::make('cn')->label(__('Common Name')),
            ])->query(
                fn (Builder $query, array $data) => $query->when(
                    $data['cn'],
                    fn (Builder $query, string $name) => $query->where(
                        'cn', 'like', '%' . trim($name) . '%'
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
            CreateAction::make()->label(__('New Signing Request')),
        ];
    }
}
