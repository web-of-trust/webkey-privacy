<?php declare(strict_types=1);

namespace App\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\{
    Column,
    GeneratedValue,
    Id,
};

/**
 * Base entity class
 * 
 * @package  App
 * @category Entity
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
abstract class BaseEntity
{
    #[Id, Column(type: Types::INTEGER), GeneratedValue(strategy: 'AUTO')]
    private readonly int $id;

    #[Column(name: 'created_by', type: Types::INTEGER, nullable: false)]
    private readonly int $createdBy;

    #[Column(name: 'updated_by', type: Types::INTEGER, nullable: false)]
    private readonly int $updatedBy;

    #[Column(name: 'created_at', type: Types::DATETIMETZ_IMMUTABLE, nullable: false)]
    private readonly DateTimeInterface $createdAt;

    #[Column(name: 'updated_at', type: Types::DATETIMETZ_IMMUTABLE, nullable: false)]
    private readonly DateTimeInterface $updatedAt;

    /**
     * Constructor
     *
     * @param int $id
     * @param int $createdBy
     * @param int $updatedBy
     * @param DateTimeInterface $createdAt
     * @param DateTimeInterface $updatedAt
     * @return self
     */
    public function __construct(
        int $id,
        int $createdBy = 0,
        int $updatedBy = 0,
        ?DateTimeInterface $createdAt = null,
        ?DateTimeInterface $updatedAt = null
    )
    {
        $this->id = $id;

        $this->createdBy = $createdBy;
        $this->updatedBy = $updatedBy;

        $this->createdAt = $createdAt ?? new \DateTimeImmutable('now');
        $this->updatedAt = $updatedAt ?? new \DateTimeImmutable('now');
    }

    /**
     * Retrieve entity id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Retrieve created by id
     *
     * @return int
     */
    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    /**
     * Retrieve updated by id
     *
     * @return int
     */
    public function getUpdatedBy(): int
    {
        return $this->updatedBy;
    }

    /**
     * Retrieve created at
     *
     * @return int
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Retrieve updated at
     *
     * @return int
     */
    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }
}
