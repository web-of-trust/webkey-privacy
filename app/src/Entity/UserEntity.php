<?php declare(strict_types=1);

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping\{
    Column,
    Entity,
    Table,
};

/**
 * User entity class
 * 
 * @package  App
 * @category Entity
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
#[Entity, Table(name: 'users')]
class UserEntity extends BaseEntity {
    const ACTIVE_STATUS = 'active';
    const LOCKED_STATUS = 'locked';
    const CLOSE_STATUS  = 'close';

    #[Column(name: 'username', type: 'string', unique: true, nullable: false)]
    private readonly string $username;

    #[Column(name: 'password', type: 'string', nullable: false)]
    private readonly string $password;

    #[Column(name: 'display_name', type: 'string', nullable: false)]
    private readonly string $displayName;

    #[Column(name: 'email', type: 'string', unique: true, nullable: false)]
    private readonly string $email;

    #[Column(name: 'status', type: 'string', columnDefinition: 'ENUM("active", "locked", "close")', nullable: false)]
    private readonly string $status;

    #[Column(name: 'login_at', type: 'datetimetz_immutable', nullable: true)]
    private readonly ?DateTimeInterface $loginAt;

    #[Column(name: 'access_at', type: 'datetimetz_immutable', nullable: true)]
    private readonly ?DateTimeInterface $accessAt;

    /**
     * Constructor
     *
     * @param int $id
     * @param string $username
     * @param string $password
     * @param string $displayName
     * @param string $email
     * @param string $status
     * @param DateTimeInterface $loginAt
     * @param DateTimeInterface $accessAt
     * @param int $createdBy
     * @param int $updatedBy
     * @param DateTimeInterface $createdAt
     * @param DateTimeInterface $updatedAt
     * @return self
     */
    public function __construct(
        int $id,
        string $username,
        string $password,
        string $displayName,
        string $email,
        string $status,
        ?DateTimeInterface $loginAt = null,
        ?DateTimeInterface $accessAt = null,
        int $createdBy = 0,
        int $updatedBy = 0,
        ?DateTimeInterface $createdAt = null,
        ?DateTimeInterface $updatedAt = null
    )
    {
        parent::__construct(
            $id, $createdBy, $updatedBy, $createdAt, $updatedAt
        );

        $this->username = $username;
        $this->password = $password;
        $this->displayName = $displayName;
        $this->email = $email;
        $this->status = $status;

        $this->loginAt = $loginAt;
        $this->accessAt = $accessAt;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Get display name
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Get login at
     *
     * @return DateTimeInterface
     */
    public function getLoginAt(): ?DateTimeInterface
    {
        return $this->loginAt;
    }

    /**
     * Get access at
     *
     * @return DateTimeInterface
     */
    public function getAccessAt(): ?DateTimeInterface
    {
        return $this->accessAt;
    }

    /**
     * User is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::ACTIVE_STATUS;
    }

    /**
     * User is locked
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->status === self::LOCKED_STATUS;
    }

    /**
     * User is close
     *
     * @return bool
     */
    public function isClose(): bool
    {
        return $this->status === self::CLOSE_STATUS;
    }
}
