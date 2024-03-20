<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources;

use App\Filament\Resources\OpenPGPCertificateResource\Pages;
use App\Models\OpenPGPCertificate;
use Filament\Resources\Resource;
use OpenPGP\Enum\{
    KeyAlgorithm,
    RevocationReasonTag,
};

/**
 * OpenPGP certificate resource
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class OpenPGPCertificateResource extends Resource
{
    protected static ?string $model = OpenPGPCertificate::class;
    protected static ?string $navigationGroup = 'OpenPGP';
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $slug = 'openpgp/certificate';

    public static function getModelLabel(): string
    {
        return __('Certificates');
    }

    public static function getNavigationLabel(): string
    {
        return __('Certificates');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOpenPGPCertificates::route('/'),
            'view' => Pages\ViewOpenPGPCertificate::route('/{record}'),
        ];
    }

    public static function exportKey(OpenPGPCertificate $model)
    {
        $filePath = tempnam(
            sys_get_temp_dir(), $model->fingerprint
        );
        file_put_contents($filePath, $model->key_data);
        return response()->download(
            $filePath, $model->fingerprint . '.asc', [
                'Content-Type' => 'application/pgp-keys',
            ]
        )->deleteFileAfterSend(true);
    }

    public static function keyAlgorithm(int $algo): string
    {
        return KeyAlgorithm::tryFrom($algo)?->name ?? '';
    }

    public static function revocationReason(int $tag): ?string
    {
        $reason = RevocationReasonTag::tryFrom($tag);
        if ($reason) {
            return match ($reason) {
                RevocationReasonTag::NoReason => __('No reason'),
                RevocationReasonTag::KeySuperseded => __('Key is superseded'),
                RevocationReasonTag::KeyCompromised => __('Key has been compromised'),
                RevocationReasonTag::KeyRetired => __('Key is retired'),
                RevocationReasonTag::UserIDInvalid => __('User ID is invalid'),
            };
        }
    }
}
