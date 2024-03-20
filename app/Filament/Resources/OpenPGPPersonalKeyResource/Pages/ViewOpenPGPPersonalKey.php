<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\OpenPGPPersonalKeyResource\Pages;

use App\Filament\Resources\{
    DomainResource,
    OpenPGPCertificateResource,
    OpenPGPPersonalKeyResource,
};
use App\Models\{
    Domain,
    OpenPGPRevocation,
};
use App\Settings\AppSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\{
    Select,
    TextInput
};
use Filament\Infolists\Components\{
    Fieldset,
    RepeatableEntry,
    TextEntry,
};
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\{
    Crypt,
    Log,
    Storage,
};
use OpenPGP\Enum\RevocationReasonTag;
use OpenPGP\OpenPGP;

/**
 * View OpenPGP personal key record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class ViewOpenPGPPersonalKey extends ViewRecord
{
    protected static string $resource = OpenPGPPersonalKeyResource::class;
    protected ?string $previousUrl = null;

    public function getTitle(): string
    {
        return __('View Personal Key');
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
                TextEntry::make('certificate.domain.name')->label(__('Domain')),
                TextEntry::make('certificate.primary_user')->label(__('User ID')),
                TextEntry::make('certificate.fingerprint')->formatStateUsing(
                    fn (string $state) => strtoupper($state)
                )->label(__('Fingerprint')),
                TextEntry::make('certificate.key_id')->formatStateUsing(
                    fn (string $state) => strtoupper($state)
                )->label(__('Key ID')),
                TextEntry::make('certificate.key_algorithm')->formatStateUsing(
                    fn (int $state) => OpenPGPCertificateResource::keyAlgorithm($state)
                )->label(__('Key Algorithm')),
                TextEntry::make('certificate.key_strength')
                    ->suffix(' bits')->label(__('Key Strength')),
                TextEntry::make('certificate.creation_time')->label(__('Creation Time')),
                TextEntry::make('certificate.expiration_time')->label(__('Expiration Time')),
            ]),
            RepeatableEntry::make('certificate.subKeys')
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
                TextEntry::make('certificate.revocation.tag')->formatStateUsing(
                    fn (int $state) => OpenPGPCertificateResource::revocationReason($state)
                )->label(__('Reason')),
                TextEntry::make('certificate.revocation.reason')->label(__('Description')),
            ])->hidden(!$this->record->is_revoked),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('revoke')
                ->form([
                    Select::make('tag')->selectablePlaceholder(false)
                        ->options([
                            RevocationReasonTag::NoReason->value => __('No reason'),
                            RevocationReasonTag::KeySuperseded->value => __('Key is superseded'),
                            RevocationReasonTag::KeyCompromised->value => __('Key has been compromised'),
                            RevocationReasonTag::KeyRetired->value => __('Key is retired'),
                            RevocationReasonTag::UserIDInvalid->value => __('User ID is invalid'),
                        ])
                        ->default(RevocationReasonTag::NoReason->value)->label(__('Reason')),
                    TextInput::make('reason')->required()->label(__('Description')),
                ])
                ->visible(!$this->record->is_revoked)
                ->action(function (array $data) {
                    $email = $this->record->user->email;
                    $parts = explode('@', $email);
                    $domain = Domain::firstWhere('name', $parts[1] ?? '');
                    if (!empty($domain->key_data)) {
                        try {
                            $settings = app(AppSettings::class);
                            $domainKey = OpenPGP::decryptPrivateKey(
                                $domain->key_data,
                                Crypt::decryptString(
                                    Storage::disk(
                                        $settings->passphraseStore()
                                    )->get(implode([
                                        DomainResource::PASSWORD_STORAGE,
                                        DIRECTORY_SEPARATOR,
                                        hash('sha256', $domain->name),
                                    ]))
                                )
                            );
                            $personalKey = $domainKey->revokeKey(
                                OpenPGP::readPrivateKey($this->record->key_data),
                                $data['reason'],
                                RevocationReasonTag::from((int) $data['tag'])
                            );
                            OpenPGPRevocation::create([
                                'certificate_id' => $this->record->certificate_id,
                                'revoke_by' => $domainKey->getFingerprint(true),
                                'tag' => $data['tag'],
                                'reason' => $data['reason'],
                            ]);
                            $this->record->update([
                                'key_data' => $personalKey->armor(),
                                'is_revoked' => true,
                            ]);
                        }
                        catch (\Throwable $e) {
                            Log::error($e);
                        }
                    }
                    redirect($this->previousUrl ?? self::getResource()::getUrl());
                })
                ->label(__('Revoke')),
            Action::make('back')->url(
                url()->previous()
            )->color('gray')->label(__('Back')),
        ];
    }
}
