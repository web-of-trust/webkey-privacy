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
use Slim\Psr7\Cookies;

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
     * @param AuthenticationInterface $authentication
     * @param TokenRepositoryInterface $tokenRepository
     * @param Cookies $cookies
     * @param LoggerInterface $logger
     * @return self
     */
    public function __construct(
        private readonly AuthenticationInterface $authentication,
        private readonly TokenRepositoryInterface $tokenRepository,
        private readonly Cookies $cookies,
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
        $user = $authentication->authenticate($request);
        if (!empty($user)) {
            $token = $tokenRepository->create($user);
            $response->getBody()->write(json_encode([
                'token' => $token->getToken(),
                'user' => $user->getIdentity(),
            ]));
            $this->cookies->set(
                TokenRepositoryInterface::TOKEN_COOKIE, $token->getToken()
            );
            return $response->withHeader(
                'Set-Cookie', $this->cookies->toHeaders()
            )->withHeader(
                'Content-Type', 'application/json'
            )->withStatus(201);
        }
        else {
            return $authentication->unauthorizedResponse($request);
        }
    }
}
