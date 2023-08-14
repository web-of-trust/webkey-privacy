<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Application;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

/**
 * Application runner class
 *
 * @package  App
 * @category Application
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
abstract class ApplicationRunner implements RunnerInterface
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
            self::env('APP_ENV') ?? ''
        ) ?? Environment::Development;

        $builder = new ContainerBuilder();
        if ($this->isProduction()) {
            $baseDir = self::env('APP_BASE_DIR') ?? BASE_DIR;
            $builder->enableCompilation($baseDir . '/var/cache');
        }
        self::loadAppConfig($builder);
        self::loadServices($builder);
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
     * Determine if the application is in the production environment.
     *
     * @return bool
     */
    protected function isProduction()
    {
        return $this->environment === Environment::Production;
    }

    /**
     * Retrieve an environment-specific configuration setting
     *
     * @param string $key
     * @return string|null
     */
    public static function env(string $key): string|null
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        return $value ?: null;
    }

    /**
     * Load application configuration
     * Configuration can be set within the .env file.
     * 
     * @param ContainerBuilder $builder
     * @return void
     */
    private static function loadAppConfig(ContainerBuilder $builder): void
    {
        $baseDir = self::env('APP_BASE_DIR') ?? BASE_DIR;
        $builder->addDefinitions($baseDir . '/app/config/app.php');
    }

    /**
     * Load services
     * 
     * @param ContainerBuilder $builder
     * @return void
     */
    private static function loadServices(ContainerBuilder $builder): void
    {
        (new ServiceDefinitions())($builder);
    }
}
