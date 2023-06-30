<?php declare(strict_types=1);

namespace App\Middleware;

use App\Authentication\UserInterface;
use App\Authorization\{
    AuthorizationInterface,
    Role,
};
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface,
};
use Psr\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface,
};
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
     * @return self
     */
    public function __construct(
        private readonly AuthorizationInterface $authorization
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
        $user = $request->getAttribute(UserInterface::class);
        if ($user instanceof UserInterface) {
            $roles = array_map(
                static fn ($role) => Role::tryFrom($role) ?? Role::AuthenticatedUser,
                $user->getRoles()
            );
            if (empty($roles)) {
                $roles = [Role::AuthenticatedUser];
            }
            foreach ($roles as $role) {
                if ($this->authorization->isGranted($role, $request)) {
                    return $handler->handle($request);
                }
            }
        }
        throw new HttpForbiddenException($request);
    }
}
