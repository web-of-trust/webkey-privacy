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
use OpenPGP\Enum\{
    CurveOid,
    DHKeySize,
    KeyType,
    RSAKeySize,
};

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
        return $form->schema([
            TextInput::make('passphrase_repo')
                ->required()->label(__('Passphrase Repository')),
            TextInput::make('webkey_dir')
                ->required()->label(__('Webkey Directory')),
            Select::make('preferred_key_type')->required()->options([
                KeyType::Rsa->name => 'RSA',
                KeyType::Dsa->name => 'DSA ElGamal',
                KeyType::Ecc->name => 'Elliptic Curve',
            ])->label(__('Preferred Key Type')),
            Select::make('preferred_ecc')->required()->options([
                CurveOid::Secp256k1->name => 'SECP 256k1 Curve',
                CurveOid::Prime256v1->name => 'NIST P-256 Curve',
                CurveOid::Secp384r1->name => 'NIST P-384 Curve',
                CurveOid::Secp521r1->name => 'NIST P-521 Curve',
                CurveOid::BrainpoolP256r1->name => 'BrainpoolP256r1 Curve',
                CurveOid::BrainpoolP384r1->name => 'BrainpoolP384r1 Curve',
                CurveOid::BrainpoolP512r1->name => 'BrainpoolP512r1 Curve',
                CurveOid::Ed25519->name => 'Curve 25519',
            ])->label(__('Preferred Elliptic Curve')),
            Select::make('preferred_rsa_size')->required()->options([
                RSAKeySize::S2048->name => RSAKeySize::S2048->value . ' bits',
                RSAKeySize::S2560->name => RSAKeySize::S2560->value . ' bits',
                RSAKeySize::S3072->name => RSAKeySize::S3072->value . ' bits',
                RSAKeySize::S3584->name => RSAKeySize::S3584->value . ' bits',
                RSAKeySize::S4096->name => RSAKeySize::S4096->value . ' bits',
            ])->label(__('Preferred RSA Key Size')),
            Select::make('preferred_dh_size')->required()->options([
                DHKeySize::L1024_N160->name => DHKeySize::L1024_N160->lSize() . ' bits',
                DHKeySize::L2048_N224->name => DHKeySize::L2048_N224->lSize() . ' bits (224)',
                DHKeySize::L2048_N256->name => DHKeySize::L2048_N256->lSize() . ' bits (256)',
                DHKeySize::L3072_N256->name => DHKeySize::L3072_N256->lSize() . ' bits',
            ])->label(__('Preferred DSA-ElGamal Key Size')),
        ]);
    }
}
