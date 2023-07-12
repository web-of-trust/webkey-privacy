<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    const COOKIE_NAME          = 'AUTH_TOKEN';
    const AUTHORIZATION_HEADER = 'Authorization';
    const BEARER_TOKEN_PATTERN = '/^Bearer\s+(.*?)$/i';
    const BASIC_TOKEN_PATTERN  = '/^Basic\s+(.*?)$/i';

    const USER_IDENTITY     = 'identity';
    const USER_DISPLAY_NAME = 'displayName';
    const USER_EMAIL        = 'email';

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
