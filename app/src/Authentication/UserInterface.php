<?php declare(strict_types=1);

namespace App\Authentication;

/**
 * User interface
 * 
 * @package  App
 * @category Authentication
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
interface UserInterface
{
    /**
     * Get the unique user identity (id, username, email address or ...)
     * 
     * @return string
     */
    function getIdentity(): string;

    /**
     * Get all user roles
     *
     * @return array<int|string, string>
     */
    function getRoles(): array;

    /**
     * Get all the details, if any
     *
     * @return array<string, mixed>
     */
    function getDetails(): array;

    /**
     * Get a detail $name if present, $default otherwise
     *
     * @param string $name
     * @param null|mixed $default
     * @return mixed
     */
    function getDetail(string $name, $default = NULL);
}
