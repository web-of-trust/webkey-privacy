<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Application;

use App\Middleware\{
    AuthenticationFilter,
    AuthorizationFilter,
};
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/**
 * Route definitions class
 * 
 * @package  App
 * @category Application
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
final class RouteDefinitions
{
    /**
     * Add routes to slim application.
     *
     * @param App $app.
     * @see https://www.slimframework.com/docs/v4/objects/routing.html
     */
    public function __invoke(App $app): void
    {
        $container = $app->getContainer();

        $app->get('/', \App\Controller\HomeController::class);
        $app->get('/logout', \App\Controller\LogoutController::class);
        $app->post(
            '/login', \App\Controller\LoginController::class
        )->addMiddleware(new AuthenticationFilter(
            $container->get(LoginAuthentication::class)
        ));

        $app->group('/rest/v1', static function (RouteCollectorProxy $group) {
            $group->get('/profile', \App\Controller\HomeController::class);
        })->addMiddleware(new AuthenticationFilter(
            $container->get(TokenAuthentication::class)
        ))->addMiddleware(new AuthorizationFilter(
            $container->get(AuthorizationInterface::class)
        ));
    }
}
