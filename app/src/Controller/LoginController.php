<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Authentication\{
    TokenRepositoryInterface,
    UserInterface,
};
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface,
};
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
     * @return self
     */
    public function __construct(
        private readonly TokenRepositoryInterface $tokenRepository,
        private readonly Cookies $cookies
    )
    {
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
