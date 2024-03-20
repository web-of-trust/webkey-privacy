<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\OpenPGPCertificateResource\Pages;

use App\Filament\Resources\OpenPGPCertificateResource;
use App\Models\Domain;
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
 * List OpenPGP certificate record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class ListCertificates extends ListRecords
{
    protected static string $resource = OpenPGPCertificateResource::class;

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('primary_user')->wrap()->label(__('User ID')),
            TextColumn::make('key_id')
                ->formatStateUsing(
                    static fn (string $state): string => strtoupper($state)
                )->label(__('Key ID')),
            TextColumn::make('key_algorithm')
                ->formatStateUsing(
                    static fn (int $state): string => self::getResource()::keyAlgorithm($state)
                )->label(__('Key Algorithm ')),
            TextColumn::make('key_strength')
                ->suffix(' bits')->label(__('Key Strength')),
            IconColumn::make('is_revoked')
                ->icon(fn (bool $state): string => match ($state) {
                    false => 'heroicon-o-x-circle',
                    true => 'heroicon-o-check-circle',
                })->color(fn (bool $state): string => match ($state) {
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
                ->action(function ($record) {
                    return self::getResource()::exportKey($record);
                }),
        ])->defaultSort('creation_time', 'desc');
    }
}
