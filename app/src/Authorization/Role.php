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
    case AnonymousUser = 'Anonymous User';
    case AuthenticatedUser = 'Authenticated User';
    case Administrator = 'Administrator';
}
