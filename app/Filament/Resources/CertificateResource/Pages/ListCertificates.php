<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\CertificateResource\Pages;

use App\Filament\Resources\CertificateResource;
use App\Models\Domain;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * List certificate record class
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class ListCertificates extends ListRecords
{
    protected static string $resource = CertificateResource::class;

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('primary_user')->label(__('User ID')),
            TextColumn::make('key_id')
                ->formatStateUsing(
                    static fn (string $state): string => strtoupper($state)
                )->label(__('Key ID')),
            TextColumn::make('creation_time')
                ->sortable()->label(__('Creation Time')),
            TextColumn::make('key_strength')
                ->suffix(' bits')->label(__('Key Strength')),
        ])->filters([
            Filter::make('user')->form([
                TextInput::make('user')->label(__('User ID')),
            ])->query(static function (Builder $query, array $data) {
                return empty($data['user']) ? $query
                    : $query->where(
                        'primary_user', 'like', '%' . $data['user'] . '%'
                    );
            }),
            Filter::make('is_revoked')->form([
                Toggle::make('is_revoked')->label(__('Is Revoked')),
            ])->query(static function (Builder $query, array $data) {
                return empty($data['is_revoked']) ? $query
                    : $query->where(
                        'is_revoked', (int) $data['is_revoked']
                    );
            }),
            SelectFilter::make('domain_id')
                ->options(
                    Domain::all()->pluck('name', 'id')
                )->label(__('Domain')),
        ])->defaultSort('creation_time', 'desc')->actions([
            ViewAction::make(),
        ]);
    }
}
