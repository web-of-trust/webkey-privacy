<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Authentication;

use DateTimeImmutable;
use DateTimeInterface;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token\RegisteredClaims;

/**
 * Jwt token repository class
 * 
 * @package  App
 * @category Authentication
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class JwtTokenRepository implements TokenRepositoryInterface
{
    /**
     * Constructor
     *
     * @param Configuration $configuration
     * @param string $issuedBy
     * @param string $identifiedBy
     * @param int $expires
     * @return self
     */
    public function __construct(
        private readonly Configuration $configuration,
        private readonly string $issuedBy,
        private readonly string $identifiedBy,
        private readonly int $expires = 0
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $id): ?TokenInterface
    {
        $token = $this->configuration->parser()->parse($id);
        if ($this->configuration->validator()->validate(
            $token,
            ...$this->configuration->validationConstraints(),
        )) {
            return new JwtToken(
                $token->toString(),
                $token->claims()->get(
                    RegisteredClaims::EXPIRATION_TIME
                ),
                array_merge(
                    $token->headers()->all(),
                    $token->claims()->all(),
                )
            );
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function create(
        UserInterface $user, ?DateTimeInterface $expiresAt = null
    ): TokenInterface
    {
        $now = new DateTimeImmutable();
        $expiresAt = $expiresAt ?? $now->setTimestamp(
            $now->getTimestamp() + $this->expires
        );
        $token = $this->configuration->builder()
            ->issuedBy($this->issuedBy)
            ->identifiedBy($this->identifiedBy)
            ->permittedFor($user->getIdentity())
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($expiresAt)
            ->withClaim(self::USER_IDENTITY, $user->getIdentity())
            ->withClaim(
                self::USER_DISPLAY_NAME, $user->getDetail(self::USER_DISPLAY_NAME)
            )
            ->withClaim(self::USER_EMAIL, $user->getDetail(self::USER_EMAIL))
            ->getToken(
                $this->configuration->signer(),
                $this->configuration->signingKey(),
            );

        return new JwtToken(
            $token->toString(),
            $expiresAt,
            array_merge(
                $token->headers()->all(),
                $token->claims()->all(),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(TokenInterface $token): void
    {
    }
}
