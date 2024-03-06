<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\CertificateResource\Pages;

use App\Filament\Resources\CertificateResource;
use Filament\Infolists\Components\{
    Fieldset,
    TextEntry,
};
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use OpenPGP\Enum\KeyAlgorithm;
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
                    static fn (int $state): string => self::keyAlgorithm($state)
                )->label(__('Key Algorithm')),
                TextEntry::make('key_strength')
                    ->suffix(' bits')->label(__('Key Strength')),
                TextEntry::make('creation_time')->label(__('Creation Time')),
                TextEntry::make('expiration_time')->label(__('Expiration Time')),
            ]),
            Fieldset::make(__('Revocation'))->schema([
                TextEntry::make('revocation.reason')->label(__('Reason')),
            ])->hidden(!$this->record->is_revoked),
            Fieldset::make(__('Certificate Key'))->schema([
                TextEntry::make('key_data')->formatStateUsing(
                    static fn (string $state): string => "<pre>{$state}</pre>"
                )->html()->columnSpan(2)->label(__('Key Data')),
            ]),
        ]);
    }

    private static function keyAlgorithm(int $algo): string
    {
        return KeyAlgorithm::tryFrom($algo)?->name ?? '';
    }
}
