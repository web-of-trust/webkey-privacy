<?php declare(strict_types=1);

namespace App\Authorization;

use Psr\Http\Message\RequestInterface;

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
     * @return bool
     */
    function isGranted(string $role, RequestInterface $request): bool;
}
