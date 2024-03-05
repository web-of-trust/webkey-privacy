<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Enums;

enum RolesEnum: string
{
    case ADMINISTRATOR      = 'administrator';
    case AUTHENTICATED_USER = 'authenticated-user';

    public function label(): string
    {
        return match ($this) {
            static::ADMINISTRATOR      => __('Administrator'),
            static::AUTHENTICATED_USER => __('Authenticated User'),
        };
    }
}
