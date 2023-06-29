<?php declare(strict_types=1);

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
