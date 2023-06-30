<?php declare(strict_types=1);

namespace App\Authentication;

use App\Entity\UserEntity;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface,
};
use Slim\Exception\HttpUnauthorizedException;

/**
 * Abstract base authentication class
 * 
 * @package  App
 * @category Authentication
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
abstract class BaseAuthentication implements AuthenticationInterface
{
    /**
     * Constructor
     *
     * @param EntityManagerInterface $entityManager
     * @return self
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function unauthorizedResponse(ServerRequestInterface $request): ResponseInterface
    {
        throw new HttpUnauthorizedException($request);
    }

    /**
     * Get user entity
     * 
     * @param string $uid
     * @return UserEntity
     */
    protected function getUserEntity(string $uid = ''): ?UserEntity
    {
        return $this->entityManager->getRepository(
            UserEntity::class
        )->findOneBy(['username' => $uid]);
    }

    /**
     * Transfer user entity to authenticated user
     * 
     * @param  UserEntity $entity
     * @return UserInterface
     */
    protected function dtoUserEntity(UserEntity $entity): UserInterface
    {
        return new AuthenticatedUser(
            $entity->getUsername(),
            $entity->getRoles(),
            [
                'id' => $entity->getId(),
                'username' => $entity->getUsername(),
                'displayName' => $entity->getDisplayName(),
                'email' => $entity->getEmail(),
                'status' => $entity->getStatus(),
            ],
        );
    }
}