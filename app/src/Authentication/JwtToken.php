<?php declare(strict_types=1);

namespace App\Authentication;

use DateTimeInterface;

/**
 * Jwt token class
 * 
 * @package  App
 * @category Authentication
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class JwtToken implements TokenInterface
{
    /**
     * Constructor
     */
    public function __construct(
        private readonly string $token,
        private readonly ?DateTimeInterface $expiresAt = null,
        private readonly array $payload = []
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAt(): ?DateTimeInterface
    {
        return $this->expiresAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
