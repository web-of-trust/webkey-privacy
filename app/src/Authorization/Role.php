<?php declare(strict_types=1);

namespace App\Authorization;

/**
 * Role enum
 * 
 * @package  App
 * @category Authorization
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
enum Role: int
{
    case AnonymousUser = 0;
    case Administrator = 1;
    case AuthenticatedUser = 2;
}
