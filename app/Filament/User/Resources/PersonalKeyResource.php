<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\PersonalKeyResource\Pages;
use App\Models\PersonalKey;
use Filament\Resources\Resource;
use Filament\Infolists\Components\{
    Fieldset,
    TextEntry,
};
use Filament\Infolists\Infolist;

/**
 * User personal key resource
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class PersonalKeyResource extends Resource
{
    const PASSPHRASE_STORAGE_ITEM = 'wkp-passphrase';

    protected static ?string $model = PersonalKey::class;
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $slug = 'personal-key';

    public static function getNavigationLabel(): string
    {
        return __('Personal Key');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
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
                        static fn (int $state): string => CertificateResource::keyAlgorithm($state)
                    )->label(__('Key Algorithm')),
                    TextEntry::make('certificate.key_strength')
                        ->suffix(' bits')->label(__('Key Strength')),
                    TextEntry::make('certificate.creation_time')->label(__('Creation Time')),
                    TextEntry::make('certificate.expiration_time')->label(__('Expiration Time')),
                ]),
                Fieldset::make(__('Revocation'))->schema([
                TextEntry::make('certificate.revocation.tag')->formatStateUsing(
                    static fn (int $state): string => CertificateResource::revocationReason($state)
                )->label(__('Reason')),
                    TextEntry::make('certificate.revocation.reason')->label(__('Description')),
                ])->hidden(static fn (?string $state): bool => !empty($state)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonalKeys::route('/'),
        ];
    }
}
