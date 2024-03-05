<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources;

use App\Filament\Resources\DomainResource\Pages;
use App\Models\Domain;
use App\Settings\AppSettings;
use Filament\Resources\Resource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    Crypt,
    Storage,
};
use OpenPGP\OpenPGP;

/**
 * Domain resource
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class DomainResource extends Resource
{
    protected static ?string $model = Domain::class;
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $slug = 'domain';

    public static function getNavigationLabel(): string
    {
        return __('Domain Manager');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDomains::route('/'),
            'create' => Pages\CreateDomain::route('/create'),
            'edit' => Pages\EditDomain::route('/{record}/edit'),
        ];
    }

    public static function generateKey(string $domain, string $email): string
    {
        $settings = app(AppSettings::class);
        $passphase = Str::password($settings->passphraseLength());

        Storage::disk($settings->passphraseStore())->put(
            hash('sha256', $domain),
            Crypt::encryptString($passphase)
        );
        return OpenPGP::generateKey(
            [$email],
            $passphase,
            $settings->preferredKeyType(),
            curve: $settings->preferredEcc(),
            rsaKeySize: $settings->preferredRsaSize(),
            dhKeySize: $settings->preferredDhSize(),
        )->armor();
    }
}
