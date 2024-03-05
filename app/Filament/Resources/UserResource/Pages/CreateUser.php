<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Forms\Form;
use Filament\Forms\Components\{
    Select,
    TextInput,
};
use Filament\Resources\Pages\CreateRecord;

/**
 * Create user record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function form(Form $form): Form
    {
        $domains = [];
        return $form->schema([
            TextInput::make('name')->required()->label(__('Name')),
            TextInput::make('email')
                ->email()->required()->endsWith(
                    static::getResource()::domainNames()
                )->label(__('Email Address')),
            TextInput::make('password')->password()
                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                ->dehydrated(fn ($state) => filled($state))
                ->required()->label(__('Password')),
            Select::make('role')->required()->options(
                static::getResource()::roles()
            )->label(__('Role')),
        ]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('User has been created!');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}