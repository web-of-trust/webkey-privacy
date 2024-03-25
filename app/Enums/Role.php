<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Enums;

/**
 * Role enum
 *
 * @package  App
 * @category Enum
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
enum Role: string
{
    case AuthenticatedUser = 'authenticated-user';
    case OpenPGPManager    = 'openpgp-manager';
    case X509Manager       = 'x509-manager';
    case Administrator     = 'administrator';

    public function label(): string
    {
        return match ($this) {
            self::AuthenticatedUser => __('Authenticated User'),
            self::OpenPGPManager    => __('OpenPGP Manager'),
            self::X509Manager       => __('X509 Manager'),
            self::Administrator     => __('Administrator'),
        };
    }
}
