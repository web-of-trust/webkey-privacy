<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\X509SigningRequestResource\Pages;

use App\Filament\Resources\X509SigningRequestResource;
use App\Enums\X509KeyAlgorithm;
use Filament\Actions\Action;
use Filament\Infolists\Components\{
    Fieldset,
    RepeatableEntry,
    TextEntry,
};
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

/**
 * View x509 signing request record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class ViewX509SigningRequest extends ViewRecord
{
    protected static string $resource = X509SigningRequestResource::class;
    protected ?string $previousUrl = null;

    public function getTitle(): string
    {
        return __('View Signing Request');
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);
        $this->previousUrl = url()->previous();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Fieldset::make(__('Certificate Information'))->schema([
                TextEntry::make('cn')->label(__('Common Name')),
                TextEntry::make('country')->label(__('Country')),
                TextEntry::make('state')->label(__('State / Province')),
                TextEntry::make('locality')->label(__('Locality')),
                TextEntry::make('organization')->label(__('Organization')),
                TextEntry::make('organization_unit')->label(__('Organization Unit')),
            ]),
            Fieldset::make(__('Key Information'))->schema([
                TextEntry::make('key_algorithm')->formatStateUsing(
                    fn (int $state) => X509KeyAlgorithm::tryFrom($state)?->label()
                )->label(__('Key Algorithm')),
                TextEntry::make('key_strength')
                    ->suffix(' bits')->label(__('Key Strength')),
            ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_key')->label(__('Export Key'))
                ->icon('heroicon-m-arrow-down-tray')
                ->action(
                    fn ($record) => self::getResource()::exportKey($record)
                ),
            Action::make('export_csr')->label(__('Export Csr'))
                ->icon('heroicon-m-arrow-down-tray')
                ->action(
                    fn ($record) => self::getResource()::exportCsr($record)
                ),
            Action::make('back')->url(
                $this->previousUrl ?? self::getResource()::getUrl('index')
            )->color('gray')->label(__('Back')),
        ];
    }
}
