<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Pages;

use App\Settings\OpenPgpSettings;
use Filament\Forms\Components\{
    Select,
    TextInput,
};
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use OpenPGP\Enum\{
    HashAlgorithm,
    SymmetricAlgorithm,
};

/**
 * OpenPgp settings manager page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class ManageOpenPgpSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $slug = 'openpgp-settings';

    protected static string $settings = OpenPgpSettings::class;

    public static function getNavigationLabel(): string
    {
        return __('OpenPgp Settings');
    }

    public function getTitle(): string
    {
        return __('OpenPgp Settings');
    }

    public function form(Form $form): Form
    {
        $disks = array_keys(config('filesystems.disks'));
        return $form->schema([
            Select::make('password_store')->required()->options(
                array_combine($disks, $disks)
            )->label(__('Password Store')),
            TextInput::make('password_length')->default(32)
                ->numeric()->minValue(32)->maxValue(64)
                ->required()->label(__('Password Length')),
            Select::make('preferred_hash')->required()->options(
                collect(HashAlgorithm::cases())->pluck('name', 'name')
            )->label(__('Preferred Hash')),
            Select::make('preferred_symmetric')->required()->options(
                collect(SymmetricAlgorithm::cases())->pluck('name', 'name')
            )->label(__('Preferred Symmetric')),
            ...OpenPgpSettings::keySettings(),
        ]);
    }
}
