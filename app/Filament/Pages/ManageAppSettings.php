<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Pages;

use App\Settings\AppSettings;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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

    protected static string $settings = AppSettings::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('passphrase_repo')->required()->label(__('Passphrase Repository')),
            TextInput::make('webkey_dir')->required()->label(__('Webkey Directory')),
            Select::make('preferred_key_type')->required()->options([
                'Rsa' => 'RSA',
                'Dsa' => 'DSA ElGamal',
                'Ecc' => 'Elliptic Curve',
            ])->label(__('Preferred Key Type')),
            Select::make('preferred_ecc')->required()->options([
                'Secp256k1' => 'SECP 256k1 Curve',
                'Prime256v1' => 'NIST P-256 Curve',
                'Secp384r1' => 'NIST P-384 Curve',
                'Secp521r1' => 'NIST P-521 Curve',
                'BrainpoolP256r1' => 'BrainpoolP256r1 Curve',
                'BrainpoolP384r1' => 'BrainpoolP384r1 Curve',
                'BrainpoolP512r1' => 'BrainpoolP512r1 Curve',
                'Curve25519' => 'Curve 25519',
            ])->label(__('Preferred Elliptic Curve')),
            Select::make('preferred_rsa_size')->required()->options([
                'S2048' => '2048 bits',
                'S2560' => '2560 bits',
                'S3072' => '3072 bits',
                'S3584' => '3584 bits',
                "S4096" => '4096 bits',
            ])->label(__('Preferred RSA Key Size')),
            Select::make('preferred_dh_size')->required()->options([
                'L1024_N160' => '1024 bits',
                'L2048_N224' => '2048 bits (224)',
                'L2048_N256' => '2048 bits (256)',
                'L3072_N256' => '3072 bits',
            ])->label(__('Preferred DSA-ElGamal Key Size')),
        ]);
    }
}
