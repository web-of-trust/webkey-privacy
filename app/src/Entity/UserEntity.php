<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\{
    ArrayCollection,
    Collection,
};
use Doctrine\DBAL\Types\Types;
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
    const STATUS_ENUM = 'ENUM("active", "locked", "close")';

    #[Column(name: 'username', type: Types::STRING, unique: true, nullable: false)]
    private readonly string $username;

    #[Column(name: 'password', type: Types::STRING, nullable: false)]
    private readonly string $password;

    #[Column(name: 'display_name', type: Types::STRING, nullable: false)]
    private readonly string $displayName;

    #[Column(name: 'email', type: Types::STRING, unique: true, nullable: false)]
    private readonly string $email;

    #[Column(name: 'status', type: Types::STRING, columnDefinition: UserEntity::STATUS_ENUM, nullable: false)]
    private readonly string $status;

    #[Column(name: 'roles', type: Types::SIMPLE_ARRAY, nullable: false)]
    private readonly array $roles;

    /**
     * Constructor
     *
     * @param int $id
     * @param string $username
     * @param string $password
     * @param string $displayName
     * @param string $email
     * @param string $status
     * @param array $roles
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
        array $roles = [],
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
        $this->roles = $roles;
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
     * Get roles
     *
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
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
