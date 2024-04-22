<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\DomainResource\Pages;

use App\Filament\Resources\DomainResource;
use App\Settings\OpenPgpSettings;
use Filament\Forms\{
    Components\Fieldset,
    Components\Textarea,
    Components\TextInput,
    Components\Toggle,
    Form,
    Get,
};
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

/**
 * Create domain record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class CreateDomain extends CreateRecord
{
    protected static string $resource = DomainResource::class;
    protected static bool $canCreateAnother = false;

    public function form(Form $form): Form
    {
        return $form->schema([
            Fieldset::make(__('Domain Information'))->schema([
                TextInput::make('name')
                    ->rules([
                        fn () => function (string $attribute, $value, \Closure $fail) {
                            if (!filter_var($value, FILTER_VALIDATE_DOMAIN)) {
                                $fail(__('The domain name is invalid.'));
                            }
                        },
                    ])
                    ->required()->unique()->label(__('Name')),
                TextInput::make('email')
                    ->rules([
                        fn (Get $get) => function (
                            string $attribute, $value, \Closure $fail
                        ) use ($get) {
                            if (!Str::endsWith($value, $get('name'))) {
                                $fail(__('The email address must match the domain name.'));
                            }
                        },
                    ])->email()->required()->unique()->label(__('Email Address')),
                TextInput::make('organization')->label(__('Organization')),
                Toggle::make('generate_key')->live()
                    ->inline(false)->label(__('Generate PGP Key')),
                Textarea::make('description')
                    ->columnSpan(2)->label(__('Description')),
            ]),
            Fieldset::make(__('Key Settings'))->schema(
                OpenPgpSettings::keySettings()
            )->hidden(fn (Get $get) => !$get('generate_key')),
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['generate_key'])) {
            $data['key_data'] = static::getResource()::generateKey(
                $data['name'], $data['email'], $data
            );
        }
        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('Domain has been created!');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl();
    }
}
