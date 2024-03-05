<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Forms\Form;
use Filament\Forms\Components\{
    Select,
    TextInput,
};
use Filament\Resources\Pages\EditRecord;

/**
 * Edit user record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->label(__('Name')),
            TextInput::make('email')->email()->required()->label(__('Email Address')),
            TextInput::make('password')->password()
                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                ->dehydrated(fn ($state) => filled($state))
                ->required(false)->label(__('Password')),
            Select::make('role')->required()->options(
                static::getResource()::roles()
            )->label(__('Role')),
        ]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('User has been saved!');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
