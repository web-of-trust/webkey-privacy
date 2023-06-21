<?php declare(strict_types=1);

namespace App\Entity;

use DateTimeInterface;
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

    #[Column(name: 'key_data', type: 'text', nullable: false)]
    private readonly string $keyData;

    #[Column(name: 'is_certified', type: 'boolean', nullable: false)]
    private readonly int $isCertified;

    #[Column(name: 'is_revoked', type: 'boolean', nullable: false)]
    private readonly int $isRevoked;

    /**
     * Constructor
     *
     * @param int $id
     * @param CertificateEntity $certificate
     * @param UserEntity $user
     * @param string $keyData
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
        string $keyData,
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
        $this->keyData = $keyData;
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
     * Get key data
     * 
     * @return string
     */
    public function getKeyData(): string
    {
        return $this->keyData;
    }
}
