<?php declare(strict_types=1);

namespace App\Controller;

use App\Authentication\{
    TokenRepositoryInterface,
    UserInterface,
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
     * @param TokenRepositoryInterface $tokenRepository
     * @param Cookies $cookies
     * @param LoggerInterface $logger
     * @return self
     */
    public function __construct(
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
        $user = $request->getAttribute(UserInterface::class);
        if ($user instanceof UserInterface) {
            $token = $tokenRepository->create($user);
            $response->getBody()->write(json_encode([
                'token' => $token->getToken(),
                'user' => [
                    'uid' => $user->getIdentity(),
                    'displayName' => $user->getDetail('displayName'),
                    'email' => $user->getDetail('email'),
                ],
            ]));
            $this->cookies->set(
                TokenRepositoryInterface::TOKEN_COOKIE, $token->getToken()
            );
            $response = $response->withHeader(
                'Set-Cookie', $this->cookies->toHeaders()
            )->withHeader(
                'Content-Type', 'application/json'
            )->withStatus(201);
        }
        return $response;
    }
}
