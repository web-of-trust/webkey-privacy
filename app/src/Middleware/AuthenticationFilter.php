<?php declare(strict_types=1);

namespace App\Middleware;

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
 * Abstract authentication filter middleware class
 * 
 * @package  App
 * @category Middleware
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
abstract class AuthenticationFilter implements MiddlewareInterface
{
    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @return self
     */
    public function __construct(
        protected readonly LoggerInterface $logger
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
        return $handler->handle(
            $this->validate($request)
        );
    }

    /**
     * Validate the http request.
     * 
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    abstract protected function validate(
        ServerRequestInterface $request
    ): ServerRequestInterface;
}
