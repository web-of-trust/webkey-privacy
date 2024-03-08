<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\CertificateResource\Pages;

use App\Filament\Resources\CertificateResource;
use App\Infolists\Components\CertificateKey;
use Filament\Actions\Action;
use Filament\Infolists\Components\{
    Fieldset,
    RepeatableEntry,
    TextEntry,
};
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;
use OpenPGP\OpenPGP;

/**
 * View certificate record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class ViewCertificate extends ViewRecord
{
    protected static string $resource = CertificateResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        $subKeys = [];
        $publicKey = OpenPGP::readPublicKey($this->record->key_data);
        foreach ($publicKey->getSubkeys() as $subKey) {
            $subKeys[] = self::getResource()::subKey($subKey);
        }
        $this->record->subKeys = $subKeys;
        return $infolist->schema([
            Fieldset::make(__('Certificate Information'))->schema([
                TextEntry::make('domain.name')->label(__('Domain')),
                TextEntry::make('primary_user')->label(__('User ID')),
                TextEntry::make('fingerprint')->formatStateUsing(
                    static fn (string $state): string => strtoupper($state)
                )->label(__('Fingerprint')),
                TextEntry::make('key_id')->formatStateUsing(
                    static fn (string $state): string => strtoupper($state)
                )->label(__('Key ID')),
                TextEntry::make('key_algorithm')->formatStateUsing(
                    static fn (int $state): string => self::getResource()::keyAlgorithm($state)
                )->label(__('Key Algorithm')),
                TextEntry::make('key_strength')
                    ->suffix(' bits')->label(__('Key Strength')),
                TextEntry::make('creation_time')->label(__('Creation Time')),
                TextEntry::make('expiration_time')->label(__('Expiration Time')),
            ]),
            RepeatableEntry::make('subKeys')
                ->schema([
                    TextEntry::make('fingerprint')->formatStateUsing(
                        static fn (string $state): string => strtoupper($state)
                    )->label(__('Fingerprint')),
                    TextEntry::make('key_id')->formatStateUsing(
                        static fn (string $state): string => strtoupper($state)
                    )->label(__('Key ID')),
                    TextEntry::make('key_algorithm')->label(__('Key Algorithm')),
                    TextEntry::make('key_strength')->suffix(' bits')->label(__('Key Strength')),
                    TextEntry::make('creation_time')->dateTime()->label(__('Creation Time')),
                    TextEntry::make('expiration_time')->dateTime()->label(__('Expiration Time')),
                ])->columns(2)->columnSpan(2)->label(__('Sub Keys')),
            Fieldset::make(__('Revocation'))->schema([
                TextEntry::make('revocation.reason')->label(__('Reason')),
            ])->hidden(!$this->record->is_revoked),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->action(function ($record) {
                    $filePath = 'certificates/' . $record->fingerprint . '.pgp';
                    if (!Storage::exists($filePath)) {
                        Storage::put(
                            $filePath,
                            $record->key_data
                        );
                    }
                    return Storage::download(
                        $filePath,
                        $record->primary_user . '.pgp'
                    );
                })
                ->label(__('Export Certificate')),
            Action::make('back')->url(
                url()->previous()
            )->color('gray')->label(__('Back')),
        ];
    }
}
