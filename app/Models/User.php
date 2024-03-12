<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Models;

use App\Enums\{
    PanelsEnum,
    RolesEnum,
};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * User model
 *
 * @package  App
 * @category Models
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * User can access panel in production.
     *
     * @param Panel $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }
        if ($panel->getId() === PanelsEnum::User->value) {
            return $this->hasRole(RolesEnum::AuthenticatedUser);
        }
        return false;
    }

    /**
     * User is administrator.
     *
     * @return bool
     */
    public function isAdministrator(): bool
    {
        return $this->isSupperAdmin() || $this->hasRole(RolesEnum::Administrator);
    }

    /**
     * User is supper admin.
     *
     * @return bool
     */
    public function isSupperAdmin(): bool
    {
        return $this->id === 1;
    }

    /**
     * User has active personal key.
     *
     * @return bool
     */
    public function hasActivePersonalKey(): bool
    {
        return PersonalKey::where([
            'user_id' => $this->id,
            'is_revoked' => false,
        ])->count() > 0;
    }
}
