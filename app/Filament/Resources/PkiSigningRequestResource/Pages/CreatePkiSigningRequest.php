<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\PkiSigningRequestResource\Pages;

use App\Filament\Resources\PkiSigningRequestResource;
use App\Enums\KeyAlgorithmsEnum;
use App\Models\Domain;
use App\Settings\AppSettings;
use Filament\Forms\{
    Form,
    Get,
    Set,
};
use Filament\Forms\Components\{
    Actions\Action,
    Fieldset,
    Select,
    TextInput,
    Toggle,
};
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\{
    Facades\Crypt,
    Facades\Storage,
    Str,
};
use phpseclib3\Crypt\{
    Common\PrivateKey,
    EC,
    RSA,
};
use phpseclib3\File\X509;

/**
 * Create pki signing request record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class CreatePkiSigningRequest extends CreateRecord
{
    protected static string $resource = PkiSigningRequestResource::class;
    protected static bool $canCreateAnother = false;

    public function getTitle(): string
    {
        return __('Create Signing Request');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Fieldset::make(__('Certificate Information'))->schema([
                Select::make('domain_id')
                    ->options(
                        Domain::all()->pluck('name', 'id')
                    )->required()->label(__('Domain')),
                TextInput::make('common_name')
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                if (!filter_var($value, FILTER_VALIDATE_DOMAIN)) {
                                    $fail(__('The common name is invalid.'));
                                }
                            };
                        },
                        fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                            $domain = Domain::find($get('domain_id'));
                            if (!Str::endsWith($value, $domain->name)) {
                                $fail(__('The common name must match the domain name.'));
                            }
                        },
                    ])
                    ->required()->unique()->label(__('Common Name')),
                TextInput::make('country_name')->label(__('Country')),
                TextInput::make('province_name')->label(__('Province / State')),
                TextInput::make('locality_name')->label(__('Locality')),
                TextInput::make('organization_name')->label(__('Organization')),
                TextInput::make('organization_unit_name')->label(__('Organization Unit')),
            ]),
            Fieldset::make(__('Key Settings'))->schema([
                Select::make('key_algorithm')->options(
                    collect(KeyAlgorithmsEnum::cases())->pluck('name', 'value')
                )->default(
                    KeyAlgorithmsEnum::Rsa->value
                )->selectablePlaceholder(false)->label(__('Key Algorithm')),
                Select::make('rsa_key_size')->default(2048)->options([
                    2048 => '2048 bits',
                    2560 => '2560 bits',
                    3072 => '3072 bits',
                    3584 => '3584 bits',
                    4096 => '4096 bits',
                ])->selectablePlaceholder(false)->label(__('Rsa Key Size')),
                Toggle::make('with_password')->default(false)->inline(false)
                    ->live()->label(__('With Password')),
                TextInput::make('password')->readonly()
                    ->default(self::randomPassphrase())
                    ->helperText('You must remember and/or save the password.')
                    ->hidden(fn (Get $get): bool => ! $get('with_password'))
                    ->hintActions([
                        Action::make('change')
                            ->label(__('Change'))
                            ->action(function (Set $set) {
                                $set(
                                    'password', self::randomPassphrase()
                                );
                            }),
                    ])->label(__('Password')),
            ]),
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $password = false;
        if (!empty($data['with_password'])) {
            $password = $data['password'];
            $storePath = implode([
                static::getResource()::PASSPHRASE_STORAGE,
                DIRECTORY_SEPARATOR,
                hash('sha256', $data['common_name']),
            ]);
            Storage::disk(app(AppSettings::class)->passphraseStore())->put(
                $storePath,
                Crypt::encryptString($password)
            );
        }
        $keyAlgo = KeyAlgorithmsEnum::from($data['key_algorithm']);
        $privateKey = self::createKey(
            $keyAlgo, $password, $data['rsa_key_size']
        );
        $data['key_strength'] = $privateKey->getLength();
        $data['key_data'] = $privateKey->toString('PKCS8');
        $data['csr_data'] = self::createCsr($privateKey, [
            'cn' => $data['common_name'],
            'c' => $data['country_name'],
            'st' => $data['province_name'],
            'l' => $data['locality_name'],
            'o' => $data['organization_name'],
            'ou' => $data['organization_unit_name'],
        ]);

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('Signing request has been created!');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    private static function createKey(
        KeyAlgorithmsEnum $keyAlgo, $password = false, int $rsaKeySize = 2048
    ): PrivateKey
    {
        return match ($keyAlgo) {
            KeyAlgorithmsEnum::Rsa => RSA::createKey($rsaKeySize)
                ->withPassword($password),
            default => EC::createKey(strtolower($keyAlgo->name))
                ->withPassword($password),
        };
    }

    private static function createCsr(
        PrivateKey $privateKey, array $dn = []
    ): string
    {
        $x509 = new X509();
        $x509->setPrivateKey($privateKey);
        $x509->setDN($dn);
        return $x509->saveCSR($x509->signCSR());
    }

    private static function randomPassphrase(): string
    {
        return app(AppSettings::class)->randomPassphrase();
    }
}