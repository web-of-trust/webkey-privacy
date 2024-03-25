<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\X509CertificateResource\Pages;

use App\Filament\Resources\X509CertificateResource;
use App\Models\Domain;
use App\Support\Helper;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\{
    Filter,
    SelectFilter,
};
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * List x509 certificate record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class ListX509Certificates extends ListRecords
{
    protected static string $resource = X509CertificateResource::class;

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('subject_dn')->wrap()->label(__('Subject DN')),
            TextColumn::make('issuer_dn')->wrap()->label(__('Issuer DN')),
            TextColumn::make('serial_number')->formatStateUsing(
                static fn (string $state): string => strtoupper($state)
            )->label(__('Serial Number')),
            TextColumn::make('not_before')->wrap()->label(__('Not Before')),
            TextColumn::make('not_after')->wrap()->label(__('Not After')),
        ])->filters([
            Filter::make('filter')->form([
                TextInput::make('subject_dn')->label(__('Subject DN')),
            ])->query(
                fn (Builder $query, array $data) => $query->when(
                    $data['subject_dn'],
                    fn (Builder $query, string $dn) => $query->where(
                        'subject_dn', 'like', '%' . trim($dn) . '%'
                    )
                )
            ),
            SelectFilter::make('domain_id')
                ->options(
                    Domain::all()->pluck('name', 'id')
                )->label(__('Domain')),
        ])->actions([
            Action::make('export_cert')->label(__('Export'))
                ->icon('heroicon-m-arrow-down-tray')
                ->action(
                    fn ($record) => Helper::exportX509Certificate($record)
                ),
        ])->emptyStateHeading(
            __('No x509 certificate yet')
        )->defaultSort('created_at', 'desc');
    }
}
