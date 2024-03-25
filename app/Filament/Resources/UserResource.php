<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources;

use App\Enums\Role;
use App\Filament\Resources\UserResource\Pages;
use App\Models\{
    Domain,
    User,
};
use Filament\Resources\Resource;

/**
 * User resource
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $slug = 'user';

    public static function getNavigationLabel(): string
    {
        return __('User Manager');
    }

    public static function roles(): array
    {
        return collect(
            Role::cases()
        )->pluck('name', 'value')->toArray();
    }

    public static function domainNames(): array
    {
         return Domain::all()->pluck('name', 'id')->toArray();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
