<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Authentication\AuthenticationInterface;
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface,
};
use Slim\Psr7\Cookies;

/**
 * Logout controller class
 * 
 * @package  App
 * @category Controller
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class LogoutController extends BaseController
{
    /**
     * Constructor
     *
     * @param Cookies $cookies
     * @return self
     */
    public function __construct(
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
        $response->getBody()->write(json_encode([
            'token' => '',
        ]));
        $this->cookies->set(
            AuthenticationInterface::COOKIE_NAME, ''
        );
        $response = $response->withHeader(
            'Set-Cookie', $this->cookies->toHeaders()
        )->withHeader(
            'Content-Type', 'application/json'
        )->withStatus(201);
        return $response;
    }
}
