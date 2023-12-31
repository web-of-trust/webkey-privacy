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
    AuthenticationInterface,
    JwtTokenRepository,
    PasswordAuthentication,
    TokenAuthentication,
    TokenRepositoryInterface,
};
use App\Authorization\{
    AclAuthorization,
    AuthorizationInterface,
};
use App\Command\Keygen\{
    EcdsaCommand,
    EddsaCommand,
    RsaCommand,
    SecretCommand,
};
use DI\ContainerBuilder;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\{
    EntityManager,
    EntityManagerInterface,
    ORMSetup,
};
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\{
    Configuration as JwtConfig,
    Signer,
};
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
use Monolog\Processor\WebProcessor;
use Psr\Container\ContainerInterface as Container;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Cookies;
use Symfony\Component\Console\Application as ConsoleApplication;

/**
 * Service definitions class
 * 
 * @package  App
 * @category Application
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
final class ServiceDefinitions
{
    /**
     * Add service definitions.
     *
     * @param ContainerBuilder $builder.
     * @see https://php-di.org/doc/php-definitions.html
     */
    public function __invoke(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            LoggerInterface::class => static function (Container $container) {
                $handlers = [
                    new RotatingFileHandler(
                        $container->get('logger.file'),
                        level: (int) $container->get('logger.level'),
                    ),
                ];
                if ($container->get('app.env') === Environment::Development->value) {
                    $handlers[] = new ErrorLogHandler(
                        level: (int) $container->get('logger.level'),
                    );
                }
                return (new Logger(
                    $container->get('logger.name')
                ))
                ->setHandlers($handlers)
                ->pushProcessor(new WebProcessor());
            },
            JwtConfig::class => static function (Container $container) {
                $signer = self::selectJwtSigner($container);
                if (self::isSymmetricSigner($signer)) {
                    $configuration = JwtConfig::forSymmetricSigner(
                        $signer,
                        InMemory::file(
                            $container->get('jwt.sign_key_file')
                        ),
                    );
                }
                else {
                    $configuration = JwtConfig::forAsymmetricSigner(
                        $signer,
                        InMemory::file(
                            $container->get('jwt.sign_key_file')
                        ),
                        InMemory::file(
                            $container->get('jwt.verify_key_file')
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
                    paths: [
                        dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Entity',
                    ],
                    isDevMode: $container->get('app.env') === Environment::Development->value,
                );
                $connection = DriverManager::getConnection(
                    (new DsnParser())->parse($container->get('database.dsn')),
                    $config,
                );
                return new EntityManager($connection, $config);
            },
            TokenRepositoryInterface::class => static function (Container $container) {
                return new JwtTokenRepository(
                    $container->get(JwtConfig::class),
                    $container->get('jwt.issued_by'),
                    $container->get('jwt.identified_by'),
                    (int) $container->get('jwt.expires'),
                );
            },
            PasswordAuthentication::class => static function (Container $container) {
                return new PasswordAuthentication(
                    $container->get(EntityManagerInterface::class),
                );
            },
            AuthenticationInterface::class => static function (Container $container) {
                return new TokenAuthentication(
                    $container->get(TokenRepositoryInterface::class),
                    $container->get(EntityManagerInterface::class),
                );
            },
            AuthorizationInterface::class => static function (Container $container) {
                return new AclAuthorization(
                    $container->get('authorization.acl'),
                );
            },
            ConsoleApplication::class => static function (Container $container) {
                $console = new ConsoleApplication(
                    $container->get('app.name'),
                    $container->get('app.version'),
                );
                $console->addCommands([
                    new EcdsaCommand(),
                    new EddsaCommand(),
                    new RsaCommand(),
                    new SecretCommand(),
                ]);

                return $console;
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
     * Select jwt signer
     * 
     * @param Container $container
     * @return Signer
     */
    private static function selectJwtSigner(Container $container): Signer
    {
        return match ($container->get('jwt.algorithm')) {
            'BLAKE2B' => new \Lcobucci\JWT\Signer\Blake2b(),
            'EdDSA' => new \Lcobucci\JWT\Signer\Eddsa(),
            'RS512' => new \Lcobucci\JWT\Signer\Rsa\Sha512(),
            'RS384' => new \Lcobucci\JWT\Signer\Rsa\Sha384(),
            'RS256' => new \Lcobucci\JWT\Signer\Rsa\Sha256(),
            'ES512' => new \Lcobucci\JWT\Signer\Ecdsa\Sha512(),
            'ES384' => new \Lcobucci\JWT\Signer\Ecdsa\Sha384(),
            'ES256' => new \Lcobucci\JWT\Signer\Ecdsa\Sha256(),
            'HS512' => new \Lcobucci\JWT\Signer\Hmac\Sha512(),
            'HS384' => new \Lcobucci\JWT\Signer\Hmac\Sha384(),
            default => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
        };
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
