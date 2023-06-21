<?php declare(strict_types=1);

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
    /**
     * Authenticate the http request and return a valid user or null if not authenticated
     * 
     * @return UserInterface
     */
    function authenticate(ServerRequestInterface $request): ?UserInterface;

    /**
     * Generate the unauthorized response
     * 
     * @return ResponseInterface
     */
    function unauthorizedResponse(ServerRequestInterface $request): ResponseInterface;
}
