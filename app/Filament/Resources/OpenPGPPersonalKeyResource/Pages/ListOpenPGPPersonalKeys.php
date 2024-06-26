<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\OpenPGPPersonalKeyResource\Pages;

use App\Filament\Resources\OpenPGPPersonalKeyResource;
use App\Support\Helper;
use Filament\Actions;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\{
    Action,
    ViewAction,
};
use Filament\Tables\Columns\{
    IconColumn,
    TextColumn,
};
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * List OpenPGP personal key record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class ListOpenPGPPersonalKeys extends ListRecords
{
    protected static string $resource = OpenPGPPersonalKeyResource::class;

    public function getTitle(): string
    {
        return __('Personal Keys');
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('certificate.primary_user')
                ->searchable()->label(__('User ID')),
            TextColumn::make('certificate.key_id')
                ->formatStateUsing(
                    fn (string $state) => strtoupper($state)
                )->label(__('Key ID')),
            TextColumn::make('certificate.key_algorithm')
                ->formatStateUsing(
                    fn (int $state) => Helper::keyAlgorithm($state)
                )->label(__('Key Algorithm ')),
            TextColumn::make('certificate.key_strength')
                ->suffix(' bits')->label(__('Key Strength')),
            IconColumn::make('is_revoked')
                ->icon(fn (bool $state) => match ($state) {
                    false => 'heroicon-o-x-circle',
                    true => 'heroicon-o-check-circle',
                })->color(fn (bool $state) => match ($state) {
                    false => 'success',
                    true => 'danger',
                })->label(__('Is Revoked')),
            TextColumn::make('certificate.creation_time')
                ->sortable()->label(__('Creation Time')),
        ])->filters([
            Filter::make('filter')->form([
                Toggle::make('revoked')->label(__('Is Revoked')),
            ])->baseQuery(
                fn (Builder $query) => $query->select('openpgp_personal_keys.*')->leftJoin(
                    'openpgp_certificates', 'openpgp_certificates.id', '=', 'openpgp_personal_keys.certificate_id'
                )
            )->query(
                fn (Builder $query, array $data) => $query->when(
                    $data['revoked'],
                    fn (Builder $query, int $revoked) => $query->where(
                        'openpgp_personal_keys.is_revoked', $revoked
                    )
                )
            ),
        ])->emptyStateHeading(
            __('No personal key yet')
        )->defaultSort('certificate.creation_time', 'desc');
    }
}
