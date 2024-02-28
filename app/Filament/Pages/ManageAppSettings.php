<?php

namespace App\Filament\Pages;

use App\Settings\AppSettings;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageAppSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = AppSettings::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('webkey_store')->label(__('Webkey Store')),
        ]);
    }
}
