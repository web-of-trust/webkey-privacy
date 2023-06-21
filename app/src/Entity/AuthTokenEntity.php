<?php declare(strict_types=1);

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping\{
    Column,
    Entity,
    Table,
};

/**
 * Auth token entity class
 * 
 * @package  App
 * @category Entity
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
#[Entity, Table(name: 'auth_tokens')]
class AuthTokenEntity extends BaseEntity {
    #[Column(name: 'token', type: 'string', unique: true, nullable: false)]
    private readonly string $token;

    #[Column(name: 'data', type: 'text', nullable: false)]
    private readonly string $data;

    #[Column(name: 'issued_at', type: 'datetimetz_immutable', nullable: false)]
    private readonly DateTimeInterface $issuedAt;

    #[Column(name: 'expires_at', type: 'datetimetz_immutable', nullable: false)]
    private readonly DateTimeInterface $expiresAt;

    /**
     * Constructor
     *
     * @param int $id
     * @param string $token
     * @param string $data
     * @param DateTimeInterface $issuedAt
     * @param DateTimeInterface $expiresAt
     * @param int $createdBy
     * @param int $updatedBy
     * @param DateTimeInterface $createdAt
     * @param DateTimeInterface $updatedAt
     * @return self
     */
    public function __construct(
        int $id,
        string $token,
        string $data,
        ?DateTimeInterface $issuedAt = null,
        ?DateTimeInterface $expiresAt = null,
        int $createdBy = 0,
        int $updatedBy = 0,
        ?DateTimeInterface $createdAt = null,
        ?DateTimeInterface $updatedAt = null
    )
    {
        parent::__construct(
            $id, $createdBy, $updatedBy, $createdAt, $updatedAt
        );

        $this->token = $token;
        $this->data = $data;
        $this->issuedAt = $issuedAt ?? new \DateTimeImmutable('now');
        $this->expiresAt = $expiresAt ?? new \DateTimeImmutable('now');
    }

    /**
     * Get authentication token
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Get authentication token data
     *
     * @return DateTimeInterface
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * Get issued at
     *
     * @return DateTimeInterface
     */
    public function getIssuedAt(): DateTimeInterface
    {
        return $this->issuedAt;
    }

    /**
     * Get expiry
     *
     * @return DateTimeInterface
     */
    public function getExpiresAt(): DateTimeInterface
    {
        return $this->expiresAt;
    }

    /**
     * Check token is expire
     *
     * @return bool
     */
    public function isExpire(): bool
    {
        return $this->expiresAt->getTimestamp() < time();
    }
}
