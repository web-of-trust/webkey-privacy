<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\DomainResource\Pages;

use App\Filament\Resources\DomainResource;
use App\Settings\AppSettings;
use Filament\Forms\{
    Form,
    Get,
};
use Filament\Forms\Components\{
    Fieldset,
    Textarea,
    TextInput,
    Toggle,
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

    public function form(Form $form): Form
    {
        return $form->schema([
            Fieldset::make(__('Domain Information'))->schema([
                TextInput::make('name')
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                if (!filter_var($value, FILTER_VALIDATE_DOMAIN)) {
                                    $fail('The domain name is invalid.');
                                }
                            };
                        },
                    ])
                    ->required()->unique()->label(__('Name')),
                TextInput::make('email')
                    ->rules([
                        fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                            if (!Str::endsWith($value, $get('name'))) {
                                $fail('The email address must match the domain name.');
                            }
                        },
                    ])->email()->required()->unique()->label(__('Email Address')),
                TextInput::make('organization')->label(__('Organization')),
                Toggle::make('generate_key')->live()
                    ->inline(false)->label(__('Generate Domain Key')),
                Textarea::make('description')
                    ->columnSpan(2)->label(__('Description')),
            ]),
            Fieldset::make(__('Key Settings'))->schema(
                AppSettings::keySettings()
            )->hidden(fn (Get $get): bool => ! $get('generate_key')),
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
        return static::getResource()::getUrl('index');
    }
}
