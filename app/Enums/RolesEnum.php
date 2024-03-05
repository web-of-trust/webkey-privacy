<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Enums;

/**
 * Roles enum
 *
 * @package  App
 * @category Enum
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
enum RolesEnum: string
{
    case AUTHENTICATED_USER = 'authenticated-user';
    case ADMINISTRATOR      = 'administrator';

    public function label(): string
    {
        return match ($this) {
            self::AUTHENTICATED_USER => __('Authenticated User'),
            self::ADMINISTRATOR      => __('Administrator'),
        };
    }
}
