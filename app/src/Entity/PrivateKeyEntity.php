<?php declare(strict_types=1);

namespace App\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\{
    Column,
    Entity,
    JoinColumn,
    ManyToOne,
    OneToOne,
    Table,
};

/**
 * Private key entity class
 * 
 * @package  App
 * @category Entity
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
#[Entity, Table(name: 'private_keys')]
class PrivateKeyEntity extends BaseEntity
{
    #[OneToOne(targetEntity: CertificateEntity::class)]
    #[JoinColumn(name: 'certificate_id', referencedColumnName: 'id')]
    private readonly CertificateEntity $certificate;

    #[ManyToOne(targetEntity: UserEntity::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private readonly UserEntity $user;

    #[Column(name: 'private_key_data', type: Types::TEXT, nullable: false)]
    private readonly string $privateKeyData;

    /**
     * Constructor
     *
     * @param int $id
     * @param CertificateEntity $certificate
     * @param UserEntity $user
     * @param string $privateKeyData
     * @param int $createdBy
     * @param int $updatedBy
     * @param DateTimeInterface $createdAt
     * @param DateTimeInterface $updatedAt
     * @return self
     */
    public function __construct(
        int $id,
        CertificateEntity $certificate,
        UserEntity $user,
        string $privateKeyData,
        int $createdBy = 0,
        int $updatedBy = 0,
        ?DateTimeInterface $createdAt = null,
        ?DateTimeInterface $updatedAt = null
    )
    {
        parent::__construct(
            $id, $createdBy, $updatedBy, $createdAt, $updatedAt
        );

        $this->certificate = $certificate;
        $this->user = $user;
        $this->privateKeyData = $privateKeyData;
    }

    /**
     * Get certificate entity
     * 
     * @return CertificateEntity
     */
    public function getCertificate(): CertificateEntity
    {
        return $this->certificate;
    }

    /**
     * Get user entity
     * 
     * @return UserEntity
     */
    public function getUser(): UserEntity
    {
        return $this->user;
    }

    /**
     * Get private key data
     * 
     * @return string
     */
    public function getPrivateKeyData(): string
    {
        return $this->privateKeyData;
    }
}
