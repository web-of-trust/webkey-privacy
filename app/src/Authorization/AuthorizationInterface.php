<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Authorization;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Authorization interface
 * 
 * @package  App
 * @category Authorization
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
interface AuthorizationInterface
{
    /**
     * Check if a role is granted for the request
     * 
     * @param Role $role
     * @param ServerRequestInterface $request
     * @return bool
     */
    function isGranted(Role $role, ServerRequestInterface $request): bool;
}
