<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\X509SigningRequestResource\Pages;

use App\Filament\Resources\X509SigningRequestResource;
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
 * Create x509 signing request record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class CreateX509SigningRequest extends CreateRecord
{
    protected static string $resource = X509SigningRequestResource::class;
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
                TextInput::make('cn')->rules([
                    function () {
                        return function (string $attribute, mixed $value, \Closure $fail) {
                            if (!filter_var($value, FILTER_VALIDATE_DOMAIN)) {
                                $fail(__('The common name is invalid.'));
                            }
                        };
                    },
                    fn (Get $get): \Closure => function (
                        string $attribute, mixed $value, \Closure $fail
                    ) use ($get) {
                        $domain = Domain::find($get('domain_id'));
                        if (!Str::endsWith($value, $domain->name)) {
                            $fail(__('The common name must match the domain name.'));
                        }
                    },
                ])->required()->label(__('Common Name')),
                TextInput::make('country')->rules([
                    function () {
                        return function (string $attribute, mixed $value, \Closure $fail) {
                            if (strtoupper($value) !== $value) {
                                $fail(__('The country must be uppercase.'));
                            }
                        };
                    },
                ])->label(__('Country')),
                TextInput::make('state')->label(__('State / Province')),
                TextInput::make('locality')->label(__('Locality')),
                TextInput::make('organization')->label(__('Organization')),
                TextInput::make('organization_unit')->label(__('Organization Unit')),
            ]),
            Fieldset::make(__('Key Settings'))->schema([
                Select::make('key_algorithm')->options(
                    collect(
                        array_map(function ($keyAlgo) {
                            return [
                                'label' => $keyAlgo->label(),
                                'value' => $keyAlgo->value,
                            ];
                        }, KeyAlgorithmsEnum::cases())
                    )->pluck('label', 'value')
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
                    ->hidden(fn (Get $get): bool => !$get('with_password'))
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
        $password = $data['password'] ?? null;
        $keyAlgo = KeyAlgorithmsEnum::from((int) $data['key_algorithm']);
        $privateKey = self::createKey(
            $keyAlgo, $password, $data['rsa_key_size']
        );
        $data['key_strength'] = $privateKey->getLength();
        $data['key_data'] = $privateKey->toString('PKCS8');
        $data['csr_data'] = self::createCsr($privateKey, [
            'cn' => $data['cn'],
            'c' => $data['country'],
            'st' => $data['state'],
            'l' => $data['locality'],
            'o' => $data['organization'],
            'ou' => $data['organization_unit'],
        ]);

        if (!empty($password)) {
            $storePath = implode([
                static::getResource()::PASSWORD_STORAGE,
                DIRECTORY_SEPARATOR,
                $privateKey->getPublickey()->getFingerprint('sha256'),
            ]);
            Storage::disk(app(AppSettings::class)->passphraseStore())->put(
                $storePath,
                Crypt::encryptString($password)
            );
        }

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
        KeyAlgorithmsEnum $keyAlgo,
        ?string $password = null,
        int $rsaKeySize = 2048
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
