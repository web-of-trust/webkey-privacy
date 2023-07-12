<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        )->findOneBy([
            'username' => $uid,
            'status' => UserEntity::ACTIVE_STATUS,
        ]);
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
