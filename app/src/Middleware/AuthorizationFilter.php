<?php declare(strict_types=1);

namespace App\Middleware;

use App\Authorization\AuthorizationInterface;
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface,
};
use Psr\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface,
};
use Psr\Log\LoggerInterface;

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
     * @throws HttpForbiddenException
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
        return $handler->handle($request);
    }
}
