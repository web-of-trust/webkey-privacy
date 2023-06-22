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
 * Default authentication class
 * 
 * @package  App
 * @category Authentication
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class DefaultAuthentication implements AuthenticationInterface
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
    public function authenticate(
        ServerRequestInterface $request
    ): ?UserInterface
    {
        $parsedBody = $request->getParsedBody();
        if (is_array($parsedBody)) {
            $username = filter_var(
                $parsedBody['username'] ?? '',
                FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            );
            $password = $parsedBody['password'] ?? '';
            $entity = $entityManager->getRepository(
                UserEntity::class
            )->findOneBy(['username' => $username]);
            if (!empty($entity) &&
                password_verify($password, $entity->getPassword())) {
                return new DefaultUser(
                    $entity->getUsername(),
                    [],
                    [
                        'id' => $entity->getId(),
                        'username' => $entity->getUsername(),
                        'displayName' => $entity->displayName(),
                        'email' => $entity->getEmail(),
                        'status' => $entity->getStatus(),
                    ],
                );
            }
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function unauthorizedResponse(ServerRequestInterface $request): ResponseInterface
    {
        throw new HttpUnauthorizedException($request);
    }
}
