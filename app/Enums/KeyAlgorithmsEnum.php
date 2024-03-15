<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Enums;

/**
 * Key algorithms enum
 *
 * @package  App
 * @category Enum
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
enum KeyAlgorithmsEnum: int
{
    case Rsa = 1;
    case EcP256 = 3;
    case EcP384 = 4;
    case EcP521 = 5;
    case Ed25519 = 6;
}
