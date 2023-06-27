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
            $user = $this->entityManager->getRepository(
                UserEntity::class
            )->findOneBy(['username' => $username]);
            if (!empty($user) && password_verify($password, $user->getPassword())) {
                $roles = [];
                foreach ($user->getRoles() as $role) {
                    $roles[$role->getId()] = $role->getName();
                }
                return new DefaultUser(
                    $user->getUsername(),
                    $roles,
                    [
                        'id' => $user->getId(),
                        'username' => $user->getUsername(),
                        'displayName' => $user->getDisplayName(),
                        'email' => $user->getEmail(),
                        'status' => $user->getStatus(),
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
