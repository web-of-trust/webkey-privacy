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
use Filament\Actions;
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

    public function getTitle(): string
    {
        return __('View Signing Request');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Fieldset::make(__('Certificate Information'))->schema([
                TextEntry::make('cn')->label(__('Common Name')),
                TextEntry::make('country')->label(__('Country')),
                TextEntry::make('province')->label(__('Province / State')),
                TextEntry::make('locality')->label(__('Locality')),
                TextEntry::make('organization')->label(__('Organization')),
                TextEntry::make('organization_unit')->label(__('Organization Unit')),
            ]),
            Fieldset::make(__('Key Information'))->schema([
                TextEntry::make('key_algorithm')->formatStateUsing(
                    static fn (int $state): string => KeyAlgorithmsEnum::tryFrom($state)->name
                )->label(__('Key Algorithm')),
                TextEntry::make('key_strength')
                    ->suffix(' bits')->label(__('Key Strength')),
            ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
