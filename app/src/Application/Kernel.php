<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Application;
use App\Authentication\{
    AuthenticationInterface,
    DefaultAuthentication,
    JwtTokenRepository,
    TokenRepositoryInterface,
};
use App\Middleware\JwtAuthenticationFilter;
use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\{
    EntityManager,
    EntityManagerInterface,
    ORMSetup,
};
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration as JwtConfiguration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\{
    IdentifiedBy,
    IssuedBy,
    PermittedFor,
    SignedWith,
    StrictValidAt,
};
use Monolog\Logger;
use Monolog\Handler\{
    ErrorLogHandler,
    RotatingFileHandler,
};
use Psr\Container\ContainerInterface as Container;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Psr7\Cookies;
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
     * @var Container
     */
    private static ?Container $container = null;

    /**
     * Slim application
     *
     * @var App
     */
    private static ?App $app = null;

    /**
     * Initialize application.
     *
     * @param string $mode
     * @return void
     */
    public static function initialize(?string $mode = null): void
    {
        if (empty(self::$container)) {
            $mode = $mode ?? self::getEnvValue('app.mode') ?? self::DEVELOPMENT_MODE;
            $builder = new ContainerBuilder();
            if ($mode === self::PRODUCTION_MODE) {
                $baseDir = self::getEnvValue('app.base_dir') ?? BASE_DIR;
                $builder->enableCompilation($baseDir . '/var/cache/app');
            }
            self::registerConfig($builder);
            self::registerServices($builder);
            self::$container = $builder->build();
        }
    }

    /**
     * Start application and serve user requests.
     *
     * @return void
     */
    public static function serve(): void
    {
        self::initialize();

        if (empty(self::$app)) {
            self::$app = Bridge::create(self::$container);
            self::registerMiddlewares(self::$app);
            self::registerRoutes(self::$app);
        }

        self::$app->run();
    }

    /**
     * Get Psr container
     *
     * @return Container
     */
    public static function getContainer(): Container
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
        return match (true) {
            array_key_exists($key, $_ENV) => $_ENV[$key],
            array_key_exists($key, $_SERVER) => $_SERVER[$key],
            default => getenv($key) ?: null,
        };
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
        $baseDir = self::getEnvValue('app.base_dir') ?? BASE_DIR;
        $builder->addDefinitions($baseDir . '/app/config/app.php');
    }

    /**
     * Register services
     * 
     * @param ContainerBuilder $builder
     * @return void
     */
    private static function registerServices(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            LoggerInterface::class => static function (Container $container) {
                return (new Logger(
                    $container->get('logger.name')
                ))->setHandlers([
                    new ErrorLogHandler(
                        level: (int) $container->get('logger.level'),
                    ),
                    new RotatingFileHandler(
                        $container->get('logger.file'),
                        level: (int) $container->get('logger.level'),
                    ),
                ]);
            },
            JwtConfiguration::class => static function (Container $container) {
                $signer = self::selectJwtSigner($container);
                if (self::isSymmetricSigner($signer)) {
                    $configuration = JwtConfiguration::forSymmetricSigner(
                        $signer,
                        InMemory::base64Encoded(
                            $container->get('jwt.sign_key')
                        ),
                    );
                }
                else {
                    $configuration = JwtConfiguration::forAsymmetricSigner(
                        $signer,
                        InMemory::base64Encoded(
                            $container->get('jwt.sign_key')
                        ),
                        InMemory::base64Encoded(
                            $container->get('jwt.verify_key')
                        ),
                    );
                }
                $configuration->setValidationConstraints(
                    new SignedWith(
                        $configuration->signer(),
                        $configuration->verificationKey(),
                    ),
                    new StrictValidAt(SystemClock::fromSystemTimezone()),
                    new IssuedBy($container->get('jwt.issued_by')),
                    new IdentifiedBy($container->get('jwt.identified_by')),
                );
                return $configuration;
            },
            EntityManagerInterface::class => static function (Container $container) {
                $config = ORMSetup::createAttributeMetadataConfiguration(
                    paths: [$container->get('database.metadata_dir')],
                    isDevMode: $container->get('app.mode') !== self::PRODUCTION_MODE,
                );
                $connection = DriverManager::getConnection(
                    (new DsnParser())->parse($container->get('database.dsn')),
                    $config,
                );
                return new EntityManager($connection, $config);
            },
            AuthenticationInterface::class => static function (Container $container) {
                return new DefaultAuthentication(
                    $container->get(EntityManagerInterface::class),
                );
            },
            TokenRepositoryInterface::class => static function (Container $container) {
                return new JwtTokenRepository(
                    $container->get(LoggerInterface::class),
                    $container->get(JwtConfiguration::class),
                    [
                        'expires'       => $container->get('jwt.expires'),
                        'issued_by'     => $container->get('jwt.issued_by'),
                        'identified_by' => $container->get('jwt.identified_by'),
                    ],
                );
            },
            Cookies::class => static function (Container $container) {
                return (new Cookies())->setDefaults([
                    'domain' => $container->get('cookie.domain'),
                    'hostonly' => $container->get('cookie.hostonly'),
                    'path' => $container->get('cookie.path'),
                    'expires' => $container->get('cookie.expires'),
                    'secure' => $container->get('cookie.secure'),
                    'httponly' => $container->get('cookie.httponly'),
                    'samesite' => $container->get('cookie.samesite'),
                ]);
            },
        ]);
    }

    /**
     * Register middlewares
     * 
     * @param App $app
     * @return void
     */
    private static function registerMiddlewares(App $app): void
    {
        $container = $app->getContainer();
        $app->addRoutingMiddleware();
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
     * @param App $app
     * @return void
     */
    private static function registerRoutes(App $app): void
    {
        $container = $app->getContainer();

        $app->get('/', \App\Controller\HomeController::class);
        $app->post('/login', \App\Controller\LoginController::class);

        $app->group('/rest/v1', static function (RouteCollectorProxy $group) {
            $group->get('/profile', \App\Controller\HomeController::class);
        })->add(new JwtAuthenticationFilter(
            $container->get(TokenRepositoryInterface::class),
            $container->get(LoggerInterface::class),
        ));
    }

    /**
     * Select jwt signer
     * 
     * @param Container $container
     * @return Signer
     */
    private static function selectJwtSigner(Container $container): Signer
    {
        $algorithm = $container->get('jwt.sign_algorithm');
        $hash = $container->get('jwt.sign_hash');
        switch ($algorithm) {
            case 'Rsa':
                return match ($hash) {
                    'Sha512' => new \Lcobucci\JWT\Signer\Rsa\Sha512(),
                    'Sha384' => new \Lcobucci\JWT\Signer\Rsa\Sha512(),
                    default => new \Lcobucci\JWT\Signer\Rsa\Sha256(),
                };
            case 'Ecdsa':
                return match ($hash) {
                    'Sha512' => new \Lcobucci\JWT\Signer\Ecdsa\Sha512(),
                    'Sha384' => new \Lcobucci\JWT\Signer\Ecdsa\Sha512(),
                    default => new \Lcobucci\JWT\Signer\Ecdsa\Sha256(),
                };
            case 'Eddsa':
                return new \Lcobucci\JWT\Signer\Eddsa();
            case 'Blake2b':
                return new \Lcobucci\JWT\Signer\Blake2b();
            default:
                return match ($hash) {
                    'Sha512' => new \Lcobucci\JWT\Signer\Hmac\Sha512(),
                    'Sha384' => new \Lcobucci\JWT\Signer\Hmac\Sha512(),
                    default => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                };
        }
    }

    /**
     * Is jwt symmetric signer
     * 
     * @param Signer $signer
     * @return bool
     */
    private static function isSymmetricSigner(Signer $signer): bool
    {
        if ($signer instanceof \Lcobucci\JWT\Signer\Hmac ||
            $signer instanceof \Lcobucci\JWT\Signer\Blake2b) {
            return true;
        }
        return false;
    }
}
