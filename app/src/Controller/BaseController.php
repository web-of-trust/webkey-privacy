<?php declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface,
};
use Psr\Log\LoggerInterface;

/**
 * Abstract base controller class
 * 
 * @package  App
 * @category Controller
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
abstract class BaseController implements ControllerInterface
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
     * Perform action on controller by calling `self::action`.
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface
    {
        return $this->action($request, $response, $args);
    }

    /**
     * Perform action on controller
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    abstract protected function action(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface;
}
