<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\PersonalKeyResource\Pages;
use App\Models\PersonalKey;
use Filament\Resources\Resource;

/**
 * User personal key resource
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class PersonalKeyResource extends Resource
{
    const PASSPHRASE_STORAGE_ITEM = 'wkp-passphrase';

    protected static ?string $model = PersonalKey::class;
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $slug = 'personal-key';

    public static function getNavigationLabel(): string
    {
        return __('Personal Key');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonalKeys::route('/'),
            'view' => Pages\ViewPersonalKey::route('/{record}'),
        ];
    }
}
