<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\OpenPGPPersonalKeyResource\Pages;

use App\Filament\Resources\{
    OpenPGPCertificateResource,
    OpenPGPPersonalKeyResource,
};
use Filament\Actions;
use Filament\Forms\Components\{
    TextInput,
    Toggle,
};
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
                    static fn (int $state): string => OpenPGPCertificateResource::keyAlgorithm($state)
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
        ])->filters([
            Filter::make('filter')->form([
                TextInput::make('user')->label(__('User ID')),
                Toggle::make('revoked')->label(__('Is Revoked')),
            ])->baseQuery(
                fn (Builder $query) => $query->select('personal_keys.*')->leftJoin(
                    'certificates', 'certificates.id', '=', 'personal_keys.certificate_id'
                )
            )->query(
                fn (Builder $query, array $data) => $query->when(
                    $data['user'],
                    fn (Builder $query, string $user) => $query->where(
                        'certificates.primary_user', 'like', '%' . trim($user) . '%'
                    )
                )->when(
                    $data['revoked'],
                    fn (Builder $query, int $revoked) => $query->where(
                        'personal_keys.is_revoked', $revoked
                    )
                )
            ),
        ])->emptyStateHeading(
            __('No personal key yet')
        )->defaultSort('certificate.creation_time', 'desc');
    }
}
