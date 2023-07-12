<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
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
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

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
     * @var Slim\App
     */
    private static ?\Slim\App $app = null;

    /**
     * Mini cli application
     *
     * @var Minicli\App
     */
    private static ?\Minicli\App $cli = null;

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
     * @param Slim\App $app
     * @return void
     */
    private static function registerMiddlewares(\Slim\App $app): void
    {
        $container = $app->getContainer();
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
     * @param Slim\App $app
     * @return void
     */
    private static function registerRoutes(\Slim\App $app): void
    {
        (new RouteDefinitions())($app);
    }
}
