<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Enums;
use Filament\Support\Contracts\HasLabel;

/**
 * Role enum
 *
 * @package  App
 * @category Enum
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
enum Role: string implements HasLabel
{
    case AuthenticatedUser = 'authenticated-user';
    case Administrator     = 'administrator';

    public function getLabel(): string
    {
        return match ($this) {
            self::AuthenticatedUser => __('Authenticated User'),
            self::Administrator     => __('Administrator'),
        };
    }
}
