<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources;

use App\Filament\Resources\OpenPGPPersonalKeyResource\Pages;
use App\Models\OpenPGPPersonalKey;
use Filament\Resources\Resource;

/**
 * OpenPGP personal key resource
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class OpenPGPPersonalKeyResource extends Resource
{
    protected static ?string $model = OpenPGPPersonalKey::class;
    protected static ?string $navigationGroup = 'OpenPGP';
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $slug = 'openpgp/personal-key';

    public static function getNavigationLabel(): string
    {
        return __('OpenPGP Personal Keys');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOpenPGPPersonalKeys::route('/'),
            'view' => Pages\ViewOpenPGPPersonalKey::route('/{record}'),
        ];
    }
}
