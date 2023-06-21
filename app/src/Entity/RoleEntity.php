<?php declare(strict_types=1);

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping\{
    Column,
    Entity,
    Table,
};

/**
 * Role entity class
 * 
 * @package  App
 * @category Entity
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
#[Entity, Table(name: 'roles')]
class RoleEntity extends BaseEntity {
    #[Column(name: 'name', type: 'string', unique: true, nullable: false)]
    private string $name;

    #[Column(name: 'description', type: 'string', nullable: false)]
    private string $description;

    /**
     * Constructor
     *
     * @param int $id
     * @param string $name
     * @param string $description
     * @param int $createdBy
     * @param int $updatedBy
     * @param DateTimeInterface $createdAt
     * @param DateTimeInterface $updatedAt
     * @return self
     */
    public function __construct(
        int $id,
        string $name,
        string $description,
        int $createdBy = 0,
        int $updatedBy = 0,
        ?DateTimeInterface $createdAt = null,
        ?DateTimeInterface $updatedAt = null
    )
    {
        parent::__construct(
            $id, $createdBy, $updatedBy, $createdAt, $updatedAt
        );

        $this->name = $name;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
