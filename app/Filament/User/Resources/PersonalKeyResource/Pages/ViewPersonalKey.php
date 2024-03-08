<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\User\Resources\PersonalKeyResource\Pages;

use App\Filament\User\Resources\PersonalKeyResource;
use Filament\Actions\Action;
use Filament\Infolists\Components\{
    Fieldset,
    TextEntry,
};
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

/**
 * View personal key record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class ViewPersonalKey extends ViewRecord
{
    protected static string $resource = PersonalKeyResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Fieldset::make(__('Certificate Information'))->schema([
                TextEntry::make('certificate.domain.name')->label(__('Domain')),
                TextEntry::make('certificate.primary_user')->label(__('User ID')),
                TextEntry::make('certificate.fingerprint')->formatStateUsing(
                    static fn (string $state): string => strtoupper($state)
                )->label(__('Fingerprint')),
                TextEntry::make('certificate.key_id')->formatStateUsing(
                    static fn (string $state): string => strtoupper($state)
                )->label(__('Key ID')),
                TextEntry::make('certificate.key_algorithm')->formatStateUsing(
                    static fn (int $state): string => static::getResource()::keyAlgorithm($state)
                )->label(__('Key Algorithm')),
                TextEntry::make('certificate.key_strength')
                    ->suffix(' bits')->label(__('Key Strength')),
                TextEntry::make('certificate.creation_time')->label(__('Creation Time')),
                TextEntry::make('certificate.expiration_time')->label(__('Expiration Time')),
            ]),
            Fieldset::make(__('Revocation'))->schema([
                TextEntry::make('certificate.revocation.reason')->label(__('Reason')),
            ])->hidden(!$this->record->is_revoked),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
