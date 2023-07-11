<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Application;
use App\Authentication\{
    LoginAuthentication,
    TokenAuthentication,
};
use App\Authorization\{
    AuthorizationInterface,
};
use App\Middleware\{
    AuthenticationFilter,
    AuthorizationFilter,
};
use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Minicli\Factories\AppFactory as CliFactory;
use Minicli\Logging\Logger as CliLogger;
use Minicli\App as CliApp;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\App as SlimApp;
use Slim\Routing\RouteCollectorProxy;

/**
 * Kernel class
 *
 * @package  App
 * @category Application
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
final class Kernel implements KernelInterface
{
    /**
     * Psr container
     *
     * @var ContainerInterface
     */
    private static ?ContainerInterface $container = null;

    /**
     * Slim application
     *
     * @var SlimApp
     */
    private static ?SlimApp $app = null;

    /**
     * Mini cli application
     *
     * @var CliApp
     */
    private static ?CliApp $cli = null;

    /**
     * Application environment
     *
     * @var Environment
     */
    private static ?Environment $env = null;

    /**
     * Initialize application.
     *
     * @param string $env
     * @return void
     */
    public static function initialize(?Environment $env = null): void
    {
        self::$env = $env ?? Environment::tryFrom(
            self::getEnvValue('APP_ENV') ?? ''
        ) ?? Environment::Development;

        if (empty(self::$container)) {
            $builder = new ContainerBuilder();
            if (self::$env === Environment::Production) {
                $baseDir = self::getEnvValue('APP_BASE_DIR') ?? BASE_DIR;
                $builder->enableCompilation($baseDir . '/var/cache/app');
            }
            self::registerConfig($builder);
            self::registerServices($builder);
            self::$container = $builder->build();
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function serve(?ServerRequestInterface $request = null): void
    {
        self::initialize();

        if (empty(self::$app)) {
            self::$app = Bridge::create(self::$container);
            self::registerMiddlewares(self::$app);
            self::registerRoutes(self::$app);
        }

        self::$app->run($request);
    }

    /**
     * {@inheritdoc}
     */
    public static function runCommand(array $argv = []): void
    {
        self::initialize();
        if (empty(self::$cli)) {
            self::$cli = CliFactory::make([
                'app_name' => self::$container->get('cli.name'),
                'app_path' => [
                    dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Command',
                    '@minicli/command-help'
                ],
                'logging' => self::$container->get('cli.logging'),
                'debug' => self::$env === Environment::Development,
            ], self::$container->get('cli.signature'));
            self::$cli->addService(
                'logs_path', fn () => self::$container->get('cli.logs_path')
            );
            self::$cli->addService(
                'logger', new CliLogger()
            );
        }
        self::$cli->runCommand($argv);
    }

    /**
     * Get Psr container
     *
     * @return ContainerInterface
     */
    public static function getContainer(): ContainerInterface
    {
        self::initialize();
        return self::$container;
    }

    /**
     * Retrieve an environment-specific configuration setting
     *
     * @param string $key
     * @return string|null
     */
    public static function getEnvValue(string $key): string|null
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        return $value ?: null;
    }

    /**
     * Register configuration
     * Configuration can be set within the .env file.
     * 
     * @param ContainerBuilder $builder
     * @return void
     */
    private static function registerConfig(ContainerBuilder $builder): void
    {
        $baseDir = self::getEnvValue('APP_BASE_DIR') ?? BASE_DIR;
        $builder->addDefinitions($baseDir . '/app/config/app.php')
                ->addDefinitions($baseDir . '/app/config/authorization.php');
    }

    /**
     * Register services
     * 
     * @param ContainerBuilder $builder
     * @return void
     */
    private static function registerServices(ContainerBuilder $builder): void
    {
        (new ServiceDefinitions())($builder);
    }

    /**
     * Register middlewares
     * 
     * @param SlimApp $app
     * @return void
     */
    private static function registerMiddlewares(SlimApp $app): void
    {
        $container = $app->getContainer();
        $app->addRoutingMiddleware();
        $app->addBodyParsingMiddleware();
        $app->addErrorMiddleware(
            (bool) $container->get('error.display'),
            (bool) $container->get('error.log'),
            (bool) $container->get('error.details'),
            $container->get(LoggerInterface::class),
        );
    }

    /**
     * Register routes
     * 
     * @param SlimApp $app
     * @return void
     */
    private static function registerRoutes(SlimApp $app): void
    {
        $container = $app->getContainer();

        $app->get('/', \App\Controller\HomeController::class);
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
