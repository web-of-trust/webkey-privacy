<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources;

use App\Filament\Resources\CertificateResource\Pages;
use App\Models\Certificate;
use Filament\Resources\Resource;
use OpenPGP\Enum\KeyAlgorithm;
use OpenPGP\Type\SubkeyInterface;

/**
 * Certificate resource
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $slug = 'certificate';

    public static function getNavigationLabel(): string
    {
        return __('Certificate Manager');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificates::route('/'),
            'view' => Pages\ViewCertificate::route('/{record}'),
        ];
    }

    public static function keyAlgorithm(int $algo): string
    {
        return KeyAlgorithm::tryFrom($algo)?->name ?? '';
    }

    public static function subKey(SubkeyInterface $subKey): mixed
    {
        return new class ($subKey) {
            function __construct(SubkeyInterface $subKey) {
                $this->fingerprint = $subKey->getFingerprint(true);
                $this->key_id = $subKey->getKeyID(true);
                $this->key_algorithm = $subKey->getKeyAlgorithm()->name;
                $this->key_strength = $subKey->getKeyStrength();
                $this->creation_time = $subKey->getCreationTime();
                $this->expiration_time = $subKey->getExpirationTime();
            }
        };
    }
}
