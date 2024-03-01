<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\DomainResource\Pages;

use App\Filament\Resources\DomainResource;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\{
    Textarea,
    TextInput,
};
use Filament\Resources\Pages\EditRecord;

/**
 * Edit domain record class
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
            Textarea::make('description')->columnSpan(2)->label(__('Description')),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
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
