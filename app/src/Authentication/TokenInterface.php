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
 * Carries information about current authentication token,
 * it's expiration time and actor provider specific payload.
 * 
 * @package  App
 * @category Authentication
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
interface TokenInterface
{
    /**
     * Get unique token id.
     * 
     * @return string
     */
    function getToken(): string;

    /**
     * Get token expiry.
     *
     * @return DateTimeInterface
     */
    function expiresAt(): ?DateTimeInterface;

    /**
     * Get token payload.
     * 
     * @return array<int|string, string>
     */
    function getPayload(): array;
}
