<?php declare(strict_types=1);

namespace App\Middleware;

use App\Authentication\{
    AuthenticationInterface,
    UserInterface,
};
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface,
};
use Psr\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface,
};

/**
 * Authentication filter middleware class
 * 
 * @package  App
 * @category Middleware
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class AuthenticationFilter implements MiddlewareInterface
{
    /**
     * Constructor
     *
     * @param AuthenticationInterface $authentication
     * @return self
     */
    public function __construct(
        protected readonly AuthenticationInterface $authentication
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
        $user = $this->authentication->authenticate($request);
        if ($user instanceof UserInterface) {
            return $handler->handle(
                $request->withAttribute(UserInterface::class, $user)
            );
        }
        return $this->authentication->unauthorizedResponse($request);
    }
}
