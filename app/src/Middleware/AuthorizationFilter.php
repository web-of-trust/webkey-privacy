<?php declare(strict_types=1);

namespace App\Middleware;

use App\Authorization\{
    AuthorizationInterface,
    Role,
};
use App\Entity\UserEntity;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface,
};
use Psr\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface,
};
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpForbiddenException;

/**
 * Authorization filter middleware class
 * 
 * @package  App
 * @category Middleware
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
final class AuthorizationFilter implements MiddlewareInterface
{
    /**
     * Constructor
     *
     * @param AuthorizationInterface $tokenRepository
     * @param EntityManagerInterface $entityManager
     * @return self
     */
    public function __construct(
        private readonly AuthorizationInterface $authorization,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * Process the request by calling `process` method.
     * 
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request, RequestHandlerInterface $handler
    ): ResponseInterface
    {
        return $this->process($request, $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function process(
        ServerRequestInterface $request, RequestHandlerInterface $handler
    ): ResponseInterface
    {
        $user = $this->entityManager->getRepository(
            UserEntity::class
        )->findOneBy(['username' => $request->getAttribute('uid')]);
        if (empty($user)) {
            throw new HttpForbiddenException($request);
        }

        $roles = array_map(
            static fn ($role) => Role::tryFrom($role) ?? Role::AuthenticatedUser,
            $user->getRoles()
        );
        if (empty($roles)) {
            $roles = [Role::AuthenticatedUser];
        }

        foreach ($roles as $role) {
            if ($authorization->isGranted($role, $request)) {
                return $handler->handle($request);
            }
        }
        throw new HttpForbiddenException($request);
    }
}
