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
    case AnonymousUser = 'Anonymous user';
    case AuthenticatedUser = 'Authenticated user';
    case Administrator = 'Administrator';
}
