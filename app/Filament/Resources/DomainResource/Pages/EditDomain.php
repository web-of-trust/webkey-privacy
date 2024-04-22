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
use Filament\Resources\Pages\EditRecord;

/**
 * Edit domain record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class EditDomain extends EditRecord
{
    protected static string $resource = DomainResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            Fieldset::make(__('Domain Information'))->schema([
                TextInput::make('name')->readonly()->label(__('Name')),
                TextInput::make('email')->readonly()->label(__('Email')),
                TextInput::make('organization')->label(__('Organization')),
                Toggle::make('generate_key')->hidden(
                    !empty($this->record->key_data)
                )->live()->inline(false)->label(__('Generate PGP Key')),
                Textarea::make('description')
                    ->columnSpan(2)->label(__('Description')),
            ]),
            Fieldset::make(__('Key Settings'))->schema(
                OpenPgpSettings::keySettings()
            )->hidden(fn (Get $get) => ! $get('generate_key')),
        ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $settings = app(OpenPgpSettings::class);
        $data['key_type'] = $settings->key_type;
        $data['elliptic_curve'] = $settings->elliptic_curve;
        $data['rsa_key_size'] = $settings->rsa_key_size;
        $data['dh_key_size'] = $settings->dh_key_size;
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['generate_key'])) {
            $data['key_data'] = static::getResource()::generateKey(
                $data['name'], $data['email'], $data
            );
        }
        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('Domain has been saved!');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl();
    }
}
