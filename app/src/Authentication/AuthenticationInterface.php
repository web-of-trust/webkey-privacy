<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Authentication;

use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface,
};

/**
 * Authentication interface
 * 
 * @package  App
 * @category Authentication
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
interface AuthenticationInterface
{
    const COOKIE_NAME          = 'AUTH_TOKEN';
    const AUTHORIZATION_HEADER = 'Authorization';
    const BEARER_TOKEN_PATTERN = '/^Bearer\s+(.*?)$/i';
    const BASIC_TOKEN_PATTERN  = '/^Basic\s+(.*?)$/i';

    /**
     * Authenticate the http request and return a valid user or null if not authenticated
     * 
     * @param ServerRequestInterface $request
     * @return UserInterface
     */
    function authenticate(ServerRequestInterface $request): ?UserInterface;

    /**
     * Generate the unauthorized response
     * 
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    function unauthorizedResponse(
        ServerRequestInterface $request
    ): ResponseInterface;
}
