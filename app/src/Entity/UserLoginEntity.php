<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
 * User login entity class
 * 
 * @package  App
 * @category Entity
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
#[Entity, Table(name: 'user_logins')]
class UserLoginEntity extends BaseEntity
{
    #[Column(name: 'username', type: Types::STRING, nullable: false)]
    private readonly string $username;

    #[Column(name: 'client_ip', type: Types::STRING, nullable: false)]
    private readonly string $clientIp;

    #[Column(name: 'user_agent', type: Types::STRING, nullable: false)]
    private readonly string $userAgent;

    #[Column(name: 'login_at', type: Types::DATETIMETZ_IMMUTABLE, nullable: true)]
    private readonly ?DateTimeInterface $loginAt;

    /**
     * Constructor
     *
     * @param int $id
     * @param string $username
     * @param string $clientIp
     * @param string $userAgent
     * @param DateTimeInterface $loginAt
     * @param int $createdBy
     * @param int $updatedBy
     * @param DateTimeInterface $createdAt
     * @param DateTimeInterface $updatedAt
     * @return self
     */
    public function __construct(
        int $id,
        string $username,
        string $clientIp,
        string $userAgent,
        ?DateTimeInterface $loginAt = null,
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
        $this->clientIp = $clientIp;
        $this->userAgent = $userAgent;
        $this->loginAt = $loginAt;
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
     * Get client ip
     *
     * @return string
     */
    public function getClientIp(): string
    {
        return $this->clientIp;
    }

    /**
     * Get user agent
     *
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
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
}
