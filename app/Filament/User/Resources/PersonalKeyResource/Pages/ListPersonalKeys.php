<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\User\Resources\PersonalKeyResource\Pages;

use App\Filament\User\Resources\PersonalKeyResource;
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
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\{
    IconColumn,
    TextColumn,
};
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Component as Livewire;
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
        return __('Personal Key');
    }

    public function table(Table $table): Table
    {
        return $table->columns([
        ])->headerActions([
            Action::make('generate_key')
                ->label(__('Generate Personal Key'))
                ->hidden(auth()->user()->hasPersonalKey())
                ->form([
                    Fieldset::make(__('Key Settings'))->schema([
                        ...AppSettings::keySettings(),
                        TextInput::make('passphrase')->readonly()
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
                ])
                ->action(function (Livewire $livewire, array $data) {
                    $passphrase = $data['passphrase'];
                    if (!empty($data['remember'])) {
                        self::rememberPassphrase($livewire, $passphrase);
                    }
                    $user = auth()->user();
                    static::$resource::getModel()::create([
                        'user_id' => $user->id,
                        'key_data' => self::generateKey(
                            $user->name, $user->email, $passphrase, $data
                        ),
                    ]);
                    redirect()->to(static::getResource()::getUrl('index'));
                }),
        ])->actions([
            ViewAction::make(),
        ]);
    }

    private static function generateKey(
        string $name,
        string $email,
        string $passphrase,
        array $keySettings = []
    ): string
    {
        $settings = app(AppSettings::class)->fill($keySettings);

        return OpenPGP::generateKey(
            [$name . " <$email>"],
            $passphrase,
            $settings->keyType(),
            curve: $settings->ellipticCurve(),
            rsaKeySize: $settings->rsaKeySize(),
            dhKeySize: $settings->dhKeySize(),
        )->armor();
    }

    private static function randomPassphrase(): string
    {
        return Str::password(
            app(AppSettings::class)->passphraseLength()
        );
    }

    private static function rememberPassphrase(
        Livewire $livewire, string $passphrase
    )
    {
        $item = implode([
            PersonalKeyResource::PASSPHRASE_STORAGE_ITEM,
            '-',
            hash('sha256', auth()->user()->email),
        ]);
        $livewire->js("localStorage.setItem('$item', '$passphrase')");
    }
}
