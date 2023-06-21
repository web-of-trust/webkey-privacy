<?php declare(strict_types=1);

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
