<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Concerns;

use App\Models\Domain;
use App\Support\Helper;
use Filament\Forms\Components\{
    TextInput,
    Toggle,
};
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
 * List OpenPGP certificate trait
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
trait ListOpenPGPCertificates
{
    public function getTitle(): string
    {
        return __('Certificates');
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('primary_user')->wrap()->label(__('User ID')),
            TextColumn::make('key_id')
                ->formatStateUsing(
                    fn (string $state) => strtoupper($state)
                )->label(__('Key ID')),
            TextColumn::make('key_algorithm')
                ->formatStateUsing(
                    fn (int $state) => Helper::keyAlgorithm($state)
                )->label(__('Key Algorithm ')),
            TextColumn::make('key_strength')
                ->suffix(' bits')->label(__('Key Strength')),
            IconColumn::make('is_revoked')
                ->icon(fn (bool $state) => match ($state) {
                    false => 'heroicon-o-x-circle',
                    true => 'heroicon-o-check-circle',
                })->color(fn (bool $state) => match ($state) {
                    false => 'success',
                    true => 'danger',
                })->label(__('Is Revoked')),
            TextColumn::make('creation_time')
                ->sortable()->label(__('Creation Time')),
        ])->filters([
            Filter::make('filter')->form([
                TextInput::make('user')->label(__('User ID')),
                Toggle::make('revoked')->label(__('Is Revoked')),
            ])->query(
                fn (Builder $query, array $data) => $query->when(
                    $data['user'],
                    fn (Builder $query, string $user) => $query->where(
                        'primary_user', 'like', '%' . trim($user) . '%'
                    )
                )->when(
                    $data['revoked'],
                    fn (Builder $query, int $revoked) => $query->where(
                        'is_revoked', $revoked
                    )
                )
            ),
            SelectFilter::make('domain_id')
                ->options(
                    Domain::all()->pluck('name', 'id')
                )->label(__('Domain')),
        ])->actions([
            Action::make('export')->label(__('Export Key'))
                ->icon('heroicon-m-arrow-down-tray')
                ->action(fn ($record) => Helper::exportOpenPGPKey(
                    $record->key_id, $record->key_data
                )),
        ])->emptyStateHeading(
            __('No certificate yet')
        )->defaultSort('creation_time', 'desc');
    }
}
