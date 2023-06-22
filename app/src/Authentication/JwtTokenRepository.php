<?php declare(strict_types=1);

namespace App\Authentication;

use DateTimeImmutable;
use DateTimeInterface;
use Lcobucci\JWT\Configuration;
use Psr\Log\LoggerInterface;

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
     * @param LoggerInterface $logger
     * @param Configuration $configuration
     * @param array $options
     * @return self
     */
    public function __construct(
        private readonly LoggerInterface $logger
        private readonly Configuration $configuration
        private readonly array $options = []
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $id): ?TokenInterface
    {
        try {
            $token = $this->configuration->parser()->parse($id);
            $this->configuration->validator()->assert(
                $token, ...$this->configuration->validationConstraints()
            );

            return new JwtToken(
                $token->toString(),
                $token->claims()->get('exp'),
                array_merge(
                    $token->headers()->all(),
                    $token->claims()->all()
                )
            );
        }
        catch (\Throwable $e) {
            $this->logger()->error($e);
        }
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
            $now->getTimestamp() + (int) $this->options['expires']
        );
        $token = $this->configuration->builder()
            ->issuedBy($this->options['issued_by'] ?? '')
            ->identifiedBy($this->options['identified_by'] ?? '')
            ->permittedFor($user->getIdentity())
            ->issuedAt($now)
            ->expiresAt($expiresAt)
            ->withClaim('displayName', $user->getDetail('displayName'))
            ->withClaim('email', $user->getDetail('email'))
            ->getToken(
                $this->configuration->signer(),
                $this->configuration->signingKey()
            );

        return new JwtToken(
            $token->toString(),
            $expiresAt,
            array_merge(
                $token->headers()->all(),
                $token->claims()->all()
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
