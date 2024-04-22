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
use App\Settings\OpenPgpSettings;
use Illuminate\Support\Facades\Storage;
use OpenPGP\OpenPGP;

/**
 * Domain resource
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class DomainResource extends AdminResource
{
    const PASSWORD_STORAGE = 'domain-openpgp-password';

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

    public static function generateKey(
        string $domain, string $email, array $keySettings = []
    ): string
    {
        $settings = app(OpenPgpSettings::class)->fill($keySettings);
        $password = $settings->randomPassword();

        Storage::disk($settings->passwordStore())->put(
            implode([
                self::PASSWORD_STORAGE,
                DIRECTORY_SEPARATOR,
                hash('sha256', $domain),
            ]),
            encrypt($password)
        );
        return OpenPGP::generateKey(
            [$email],
            $password,
            $settings->keyType(),
            curve: $settings->ellipticCurve(),
            rsaKeySize: $settings->rsaKeySize(),
            dhKeySize: $settings->dhKeySize(),
        )->armor();
    }
}
