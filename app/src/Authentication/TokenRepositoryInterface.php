<?php declare(strict_types=1);

namespace App\Authentication;

use DateTimeInterface;

/**
 * Provides the ability to store tokens in persistent repository.
 * 
 * @package  App
 * @category Authentication
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
interface TokenRepositoryInterface
{
    const TOKEN_HEADER = 'Authorization';
    const TOKEN_COOKIE = 'AUTH_TOKEN';

    /**
     * Load token by id, must return null if token not found.
     *
     * @param string $id
     * @return TokenInterface
     */
    function load(string $id): ?TokenInterface;

    /**
     * Create token based on the authenticated user.
     *
     * @param UserInterface $user
     * @param DateTimeInterface $expiresAt
     * @return TokenInterface
     */
    function create(
        UserInterface $user, ?DateTimeInterface $expiresAt = null
    ): TokenInterface;

    /**
     * Delete token from the persistent repository.
     */
    function delete(TokenInterface $token): void;
}
