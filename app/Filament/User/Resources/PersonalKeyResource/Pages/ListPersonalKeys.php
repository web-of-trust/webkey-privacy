<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\User\Resources\PersonalKeyResource\Pages;

use App\Filament\User\Resources\{
    CertificateResource,
    PersonalKeyResource,
};
use App\Models\Domain;
use App\Settings\AppSettings;
use Filament\Forms\{
    Form,
    Set,
};
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\{
    Fieldset,
    TextInput,
    Toggle,
};
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\{
    Action,
    ViewAction,
};
use Filament\Tables\Columns\{
    IconColumn,
    TextColumn,
};
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\{
    Crypt,
    Log,
    Storage,
};
use Illuminate\Support\Str;
use Livewire\Component as Livewire;
use OpenPGP\Type\PrivateKeyInterface;
use OpenPGP\OpenPGP;

/**
 * List personal key record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class ListPersonalKeys extends ListRecords
{
    protected static string $resource = PersonalKeyResource::class;

    public function getTitle(): string
    {
        return __('Personal Keys');
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('certificate.primary_user')->wrap()->label(__('User ID')),
            TextColumn::make('certificate.key_id')
                ->formatStateUsing(
                    static fn (string $state): string => strtoupper($state)
                )->label(__('Key ID')),
            TextColumn::make('certificate.key_algorithm')
                ->formatStateUsing(
                    static fn (int $state): string => CertificateResource::keyAlgorithm($state)
                )->label(__('Key Algorithm ')),
            TextColumn::make('certificate.key_strength')
                ->suffix(' bits')->label(__('Key Strength')),
            IconColumn::make('is_revoked')
                ->icon(fn (bool $state): string => match ($state) {
                    false => 'heroicon-o-x-circle',
                    true => 'heroicon-o-check-circle',
                })->color(fn (bool $state): string => match ($state) {
                    false => 'success',
                    true => 'danger',
                })->label(__('Is Revoked')),
            TextColumn::make('certificate.creation_time')
                ->sortable()->label(__('Creation Time')),
        ])->headerActions([
            Action::make('generate_key')
                ->label(__('Generate Personal Key'))
                ->hidden(auth()->user()->hasActivePersonalKey())
                ->form([
                    Fieldset::make(__('Key Settings'))->schema([
                        ...AppSettings::keySettings(),
                        TextInput::make('passphrase')
                            ->readonly()->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->default(self::randomPassphrase())
                            ->helperText('You must remember and/or save the passphrase.')
                            ->hintActions([
                                FormAction::make('change')
                                    ->label(__('Change'))
                                    ->action(function (Set $set) {
                                        $set(
                                            'passphrase', self::randomPassphrase()
                                        );
                                    }),
                            ])->label(__('Passphrase')),
                        Toggle::make('remember')->live()->default(true)->inline(false)
                            ->label(__('Save passphrase into browser storage')),
                    ]),
                ])->action(function (Livewire $livewire, array $data) {
                    $passphrase = $data['passphrase'];
                    $user = auth()->user();
                    $pgpKey = self::generateKey(
                        $user->name, $user->email, $passphrase, $data
                    );
                    static::$resource::getModel()::create([
                        'user_id' => $user->id,
                        'key_data' => $pgpKey->armor(),
                    ]);
                    if (!empty($data['remember'])) {
                        self::rememberPassphrase(
                            $livewire, $passphrase, $pgpKey->getFingerprint(true)
                        );
                    }
                    redirect(static::getResource()::getUrl('index'));
                }),
        ])->actions([
            ViewAction::make(),
            Action::make('export_key')->label(__('Export'))
                ->icon('heroicon-m-arrow-down-tray')
                ->action(function ($record) {
                    $filePath = tempnam(
                        sys_get_temp_dir(), $record->certificate->fingerprint
                    );
                    file_put_contents($filePath, $record->key_data);
                    return response()->download(
                        $filePath, $record->user->email . '.asc', [
                            'Content-Type' => 'application/pgp-keys',
                        ]
                    )->deleteFileAfterSend(true);
                }),
        ])->modifyQueryUsing(
            fn (Builder $query) => $query->where('user_id', auth()->user()->id)
        )->emptyStateHeading(
            __('No personal key yet')
        )->emptyStateDescription(
            __('Once you generate personal key, it will appear here.')
        )->defaultSort('certificate.creation_time', 'desc');
    }

    private static function generateKey(
        string $name,
        string $email,
        string $passphrase,
        array $keySettings = []
    ): PrivateKeyInterface
    {
        $settings = app(AppSettings::class)->fill($keySettings);

        $key = OpenPGP::generateKey(
            [$name . " <$email>"],
            $passphrase,
            $settings->keyType(),
            curve: $settings->ellipticCurve(),
            rsaKeySize: $settings->rsaKeySize(),
            dhKeySize: $settings->dhKeySize(),
        );

        $parts = explode('@', $email);
        $domain = Domain::firstWhere('name', $parts[1] ?? '');
        if (!empty($domain->key_data)) {
            try {
                $key = OpenPGP::decryptPrivateKey(
                    $domain->key_data,
                    Crypt::decryptString(
                        Storage::disk($settings->passphraseStore())->get(
                            hash('sha256', $domain->name),
                        )
                    )
                )->certifyKey($key);
            }
            catch (\Throwable $e) {
                Log::error($e);
            }
        }

        return $key;
    }

    private static function randomPassphrase(): string
    {
        return Str::password(
            app(AppSettings::class)->passphraseLength()
        );
    }

    private static function rememberPassphrase(
        Livewire $livewire, string $passphrase, string $fingerprint
    )
    {
        $item = implode([
            PersonalKeyResource::PASSPHRASE_STORAGE_ITEM,
            '-',
            $fingerprint,
        ]);
        $livewire->js("localStorage.setItem('$item', '$passphrase')");
    }
}
