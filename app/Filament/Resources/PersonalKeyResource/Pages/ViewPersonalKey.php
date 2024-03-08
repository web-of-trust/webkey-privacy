<?php

namespace App\Filament\Resources\PersonalKeyResource\Pages;

use App\Filament\Resources\{
    CertificateResource,
    PersonalKeyResource
};
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\{
    Fieldset,
    RepeatableEntry,
    TextEntry,
};
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use OpenPGP\OpenPGP;

class ViewPersonalKey extends ViewRecord
{
    protected static string $resource = PersonalKeyResource::class;
    protected ?string $previousUrl = null;

    public function mount(int | string $record): void
    {
        parent::mount($record);
        $this->previousUrl = url()->previous();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $subKeys = [];
        $publicKey = OpenPGP::readPublicKey($this->record->certificate->key_data);
        foreach ($publicKey->getSubkeys() as $subKey) {
            $subKeys[] = CertificateResource::subKey($subKey);
        }
        $this->record->subKeys = $subKeys;
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
                    static fn (int $state): string => CertificateResource::keyAlgorithm($state)
                )->label(__('Key Algorithm')),
                TextEntry::make('certificate.key_strength')
                    ->suffix(' bits')->label(__('Key Strength')),
                TextEntry::make('certificate.creation_time')->label(__('Creation Time')),
                TextEntry::make('certificate.expiration_time')->label(__('Expiration Time')),
            ]),
            Fieldset::make(__('Revocation'))->schema([
                TextEntry::make('certificate.revocation.reason')->label(__('Reason')),
            ])->hidden(!$this->record->is_revoked),
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
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('revoke')
                ->form([
                    TextInput::make('reason')->required()
                        ->label(__('Revocation Reason')),
                ])
                ->visible(!$this->record->is_revoked)
                ->action(function (array $data) {
                    redirect($this->previousUrl ?? self::getResource()::getUrl());
                })
                ->label(__('Revoke')),
        ];
    }
}
