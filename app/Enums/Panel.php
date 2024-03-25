<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Enums;

/**
 * Panel enum
 *
 * @package  App
 * @category Enum
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
enum Panel: string
{
    case Admin   = 'admin';
    case OpenPGP = 'openpgp';
    case X509    = 'x509';
    case User    = 'user';

    public function path(): string
    {
        return match ($this) {
            self::Admin   => 'admin',
            self::OpenPGP => 'openpgp',
            self::X509    => 'x509',
            self::User    => 'user',
        };
    }
}
