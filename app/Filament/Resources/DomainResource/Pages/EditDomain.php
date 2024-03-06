<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\DomainResource\Pages;

use App\Filament\Resources\DomainResource;
use Filament\Actions\DeleteAction;
use Filament\Forms\Form;
use Filament\Forms\Components\{
    Textarea,
    TextInput,
    Toggle,
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
            TextInput::make('name')->readonly()->label(__('Name')),
            TextInput::make('email')->readonly()->label(__('Email')),
            TextInput::make('organization')->label(__('Organization')),
            Toggle::make('generate_key')->hidden(
                $this->record->has_key
            )->inline(false)->label(__('Generate PGP Key')),
            Textarea::make('description')
                ->columnSpan(2)->label(__('Description')),
        ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['generate_key'])) {
            $data['key_data'] = static::getResource()::generateKey(
                $data['name'], $data['email']
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
        return static::getResource()::getUrl('index');
    }
}
