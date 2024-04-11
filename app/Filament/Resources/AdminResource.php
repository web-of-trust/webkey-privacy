<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources;

use Filament\Resources\Resource;

/**
 * Admin base resource
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
abstract class AdminResource extends Resource
{
    public static function canAccess(): bool
    {
        return auth()->user()->isAdministrator();
    }
}
