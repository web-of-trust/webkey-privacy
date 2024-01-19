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
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\{
    Column,
    Entity,
    Table,
};

/**
 * Certificate entity class
 * 
 * @package  App
 * @category Entity
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
#[Entity, Table(name: 'certificates')]
class CertificateEntity extends BaseEntity
{
    #[Column(name: 'certificate_data', type: Types::TEXT, nullable: false)]
    private readonly string $certificateData;

    #[Column(name: 'fingerprint', type: Types::STRING, unique: true, nullable: false)]
    private readonly string $fingerprint;

    #[Column(name: 'primary_user', type: Types::STRING, nullable: false)]
    private readonly string $primaryUser;

    #[Column(name: 'key_algorithm', type: Types::STRING, nullable: false)]
    private readonly string $keyAlgorithm;

    #[Column(name: 'key_strength', type: Types::INTEGER, nullable: false)]
    private readonly int $keyStrength;

    #[Column(name: 'creation_time', type: Types::DATETIMETZ_IMMUTABLE, nullable: false)]
    private readonly DateTimeInterface $creationTime;

    #[Column(name: 'expiration_time', type: Types::DATETIMETZ_IMMUTABLE, nullable: true)]
    private readonly ?DateTimeInterface $expirationTime;

    /**
     * Constructor
     *
     * @param int $id
     * @param string $certificateData
     * @param string $fingerprint
     * @param string $primaryUser
     * @param string $keyAlgorithm
     * @param int $keyStrength
     * @param DateTimeInterface $creationTime
     * @param DateTimeInterface $expirationTime
     * @param int $createdBy
     * @param int $updatedBy
     * @param DateTimeInterface $createdAt
     * @param DateTimeInterface $updatedAt
     * @return self
     */
    public function __construct(
        int $id,
        string $certificateData,
        string $fingerprint,
        string $primaryUser,
        string $keyAlgorithm,
        int $keyStrength,
        ?DateTimeInterface $creationTime = null,
        ?DateTimeInterface $expirationTime = null,
        int $createdBy = 0,
        int $updatedBy = 0,
        ?DateTimeInterface $createdAt = null,
        ?DateTimeInterface $updatedAt = null
    )
    {
        parent::__construct(
            $id, $createdBy, $updatedBy, $createdAt, $updatedAt
        );

        $this->certificateData = $certificateData;
        $this->fingerprint = $fingerprint;
        $this->primaryUser = $primaryUser;
        $this->keyAlgorithm = $keyAlgorithm;
        $this->keyStrength = $keyStrength;
        $this->creationTime = $creationTime ?? new \DateTimeImmutable('now');
        $this->expirationTime = $expirationTime;
    }

    /**
     * Get certificate data
     * 
     * @return string
     */
    public function getCertificateData(): string
    {
        return $this->certificateData;
    }

    /**
     * Get fingerprint
     * 
     * @return string
     */
    public function getFingerprint(): string
    {
        return $this->fingerprint;
    }

    /**
     * Get primary user
     * 
     * @return string
     */
    public function getPrimaryUser(): string
    {
        return $this->primaryUser;
    }

    /**
     * Get key algorithm
     * 
     * @return string
     */
    public function getKeyAlgorithm(): string
    {
        return $this->keyAlgorithm;
    }

    /**
     * Get key strength
     * 
     * @return int
     */
    public function getKeyStrength(): int
    {
        return $this->keyStrength;
    }

    /**
     * Get creation time
     * 
     * @return DateTimeInterface
     */
    public function getCreationTime(): DateTimeInterface
    {
        return $this->creationTime;
    }

    /**
     * Get expiration time
     * 
     * @return DateTimeInterface
     */
    public function getExpirationTime(): ?DateTimeInterface
    {
        return $this->expirationTime;
    }
}
