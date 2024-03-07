<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Pages;

use App\Settings\AppSettings;
use Filament\Forms\Components\{
    Select,
    TextInput,
};
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

/**
 * App settings manager page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class ManageAppSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $slug = 'app-settings';
    protected static string $settings = AppSettings::class;

    public static function getNavigationLabel(): string
    {
        return __('App Settings');
    }

    public function getTitle(): string
    {
        return __('App Settings');
    }

    public function form(Form $form): Form
    {
        $disks = array_keys(config('filesystems.disks'));
        return $form->schema([
            Select::make('passphrase_store')->required()->options(
                array_combine($disks, $disks)
            )->label(__('Passphrase Store')),
            TextInput::make('passphrase_length')
                ->numeric()->minValue(32)->maxValue(64)
                ->required()->label(__('Passphrase Length')),
            ...static::$settings::keySettings(),
        ]);
    }
}
