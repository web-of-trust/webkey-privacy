<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Concerns;

use App\Support\Helper;
use Filament\Actions\Action;
use Filament\Infolists\{
    Components\Fieldset,
    Components\RepeatableEntry,
    Components\TextEntry,
    Infolist,
};

/**
 * View OpenPGP certificate trait
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
trait ViewOpenPGPCertificate
{
    public function getTitle(): string
    {
        return __('View Certificate');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $this->record->subkeys = Helper::getSubkeys($this->record->key_data);
        return $infolist->schema([
            Fieldset::make(__('Certificate Information'))->schema([
                TextEntry::make('domain.name')->label(__('Domain')),
                TextEntry::make('primary_user')->label(__('User ID')),
                TextEntry::make('fingerprint')->formatStateUsing(
                    fn (string $state) => strtoupper($state)
                )->label(__('Fingerprint')),
                TextEntry::make('key_id')->formatStateUsing(
                    fn (string $state) => strtoupper($state)
                )->label(__('Key ID')),
                TextEntry::make('key_algorithm')->formatStateUsing(
                    fn (int $state) => Helper::keyAlgorithm($state)
                )->label(__('Key Algorithm')),
                TextEntry::make('key_strength')
                    ->suffix(' bits')->label(__('Key Strength')),
                TextEntry::make('creation_time')->label(__('Creation Time')),
                TextEntry::make('expiration_time')->label(__('Expiration Time')),
            ]),
            RepeatableEntry::make('subkeys')
                ->schema([
                    TextEntry::make('fingerprint')->formatStateUsing(
                        fn (string $state) => strtoupper($state)
                    )->label(__('Fingerprint')),
                    TextEntry::make('key_id')->formatStateUsing(
                        fn (string $state) => strtoupper($state)
                    )->label(__('Key ID')),
                    TextEntry::make('key_algorithm')->label(__('Key Algorithm')),
                    TextEntry::make('key_strength')->suffix(' bits')->label(__('Key Strength')),
                    TextEntry::make('creation_time')->dateTime('Y-m-d H:i:s')->label(__('Creation Time')),
                    TextEntry::make('expiration_time')->dateTime('Y-m-d H:i:s')->label(__('Expiration Time')),
                ])->columns(2)->columnSpan(2)->label(__('Sub Keys')),
            Fieldset::make(__('Revocation'))->schema([
                TextEntry::make('revocation.tag')->formatStateUsing(
                    fn (int $state) => Helper::revocationReason($state)
                )->label(__('Reason')),
                TextEntry::make('revocation.reason')->label(__('Description')),
            ])->hidden(!$this->record->is_revoked),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->action(fn ($record) => Helper::exportOpenPGPKey(
                    $record->key_id, $record->key_data
                ))
                ->icon('heroicon-m-arrow-down-tray')
                ->label(__('Export Key')),
            Action::make('back')->url(
                url()->previous()
            )->color('gray')->label(__('Back')),
        ];
    }
}
