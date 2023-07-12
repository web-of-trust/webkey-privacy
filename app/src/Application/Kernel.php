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
     * Application environment
     *
     * @var Environment
     */
    private readonly Environment $environment;

    /**
     * Psr container
     *
     * @var ContainerInterface
     */
    private readonly ContainerInterface $container;

    /**
     * Constructor
     *
     * @param Environment $environment
     * @return self
     */
    public function __construct(?Environment $environment = null) {
        $this->environment = $environment ?? Environment::tryFrom(
            self::getEnvValue('APP_ENV') ?? ''
        ) ?? Environment::Development;

        $builder = new ContainerBuilder();
        if ($this->environment === Environment::Production) {
            $baseDir = self::getEnvValue('APP_BASE_DIR') ?? BASE_DIR;
            $builder->enableCompilation($baseDir . '/var/cache');
        }
        self::initializeConfiguration($builder);
        self::registerServices($builder);
        $this->container = $builder->build();
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function serve(?ServerRequestInterface $request = null): void
    {
        $app = Bridge::create($this->container);
        self::registerMiddlewares($app);
        self::registerRoutes($app);

        $app->run($request);
    }

    /**
     * {@inheritdoc}
     */
    public function runCommand(array $argv = []): void
    {
        $cli = CliFactory::make([
            'app_name' => $this->container->get('cli.name'),
            'app_path' => [
                dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Command',
                '@minicli/command-help'
            ],
            'logging' => $this->container->get('cli.logging'),
            'debug' => $this->environment === Environment::Development,
        ], $this->container->get('cli.signature'));
        $cli->addService(
            'logs_path', fn () => $this->container->get('cli.logs_path')
        );
        $cli->addService(
            'logger', new CliLogger()
        );
        $cli->runCommand($argv);
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
     * Initialize configuration
     * Configuration can be set within the .env file.
     * 
     * @param ContainerBuilder $builder
     * @return void
     */
    private static function initializeConfiguration(ContainerBuilder $builder): void
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
