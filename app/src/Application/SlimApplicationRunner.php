<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Application;

use DI\Bridge\Slim\Bridge;
use Slim\App;

/**
 * Slim application runner class
 * Run the Slim application.
 *
 * @package  App
 * @category Application
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
final class SlimApplicationRunner extends ApplicationRunner
{
    /**
     * {@inheritdoc}
     */
    public function run(): void
    {
        $app = Bridge::create($this->getContainer());
        $this->registerMiddlewares($app);
        $this->registerRoutes($app);

        $app->run();
    }

    /**
     * Register middlewares
     * 
     * @param App $app
     * @return void
     */
    private function registerMiddlewares(App $app): void
    {
        $container = $this->getContainer();
        $app->addRoutingMiddleware();
        $app->addBodyParsingMiddleware();
        $app->addErrorMiddleware(
            displayErrorDetails: (bool) $container->get('error.display'),
            logErrors: (bool) $container->get('error.log'),
            logErrorDetails: (bool) $container->get('error.details'),
            logger: $container->get(LoggerInterface::class),
        );
    }

    /**
     * Register routes
     * 
     * @param App $app
     * @return void
     */
    private function registerRoutes(App $app): void
    {
        (new RouteDefinitions())($app);
    }
}
