<?php declare(strict_types=1);

namespace App\Controller;

use App\Authentication\{
    AuthenticationInterface,
    TokenRepositoryInterface,
};
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface,
};
use Psr\Log\LoggerInterface;

/**
 * Login controller class
 * 
 * @package  App
 * @category Controller
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class LoginController extends BaseController
{
    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @return self
     */
    public function __construct(
        private readonly AuthenticationInterface $authentication,
        private readonly TokenRepositoryInterface $tokenRepository,
        LoggerInterface $logger
    )
    {
        parent::__construct($logger);
    }

    /**
     * {@inheritdoc}
     */
    protected function action(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface
    {
        return $response;
    }
}
