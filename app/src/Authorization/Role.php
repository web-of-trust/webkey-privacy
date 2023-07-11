<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Authorization;

/**
 * Role enum
 * 
 * @package  App
 * @category Authorization
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
enum Role: string
{
    case AnonymousUser     = 'anonymous_user';
    case AuthenticatedUser = 'authenticated_user';
    case Administrator     = 'administrator';

    public function label(): string
    {
        return match($this) {
            static::AnonymousUser     => 'Anonymous User',
            static::AuthenticatedUser => 'Authenticated User',
            static::Administrator     => 'Administrator',
        };
    }
}
