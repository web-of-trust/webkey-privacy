<?php declare(strict_types=1);

namespace App\Middleware;

use App\Authentication\TokenRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpUnauthorizedException;

/**
 * Jwt authentication filter middleware class
 * 
 * @package  App
 * @category Middleware
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class JwtAuthenticationFilter extends AuthenticationFilter
{
    /**
     * Constructor
     *
     * @param TokenRepositoryInterface $tokenRepository
     * @param LoggerInterface $logger
     * @return self
     */
    public function __construct(
        private readonly TokenRepositoryInterface $tokenRepository,
        LoggerInterface $logger
    )
    {
        parent::__construct($logger);
    }

    /**
     * {@inheritdoc}
     */
    protected function validate(
        ServerRequestInterface $request
    ): ServerRequestInterface
    {
        $token = $this->tokenRepository->load(
            self::getAuthToken($request)
        );
        if (empty($token)) {
            throw new HttpUnauthorizedException($request);
        }
        $payload = $token->getPayload();
        return $request->withAttribute(
            'uid', $payload['uid'] ?? ''
        );
    }

    /**
     * Get auth token from request
     * 
     * @param ServerRequestInterface $request
     * @return string
     */
    private static function getAuthToken(ServerRequestInterface $request): ?string
    {
        $token = $request->getHeaderLine('Authorization');
        if (!empty($token) && preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            return $matches[1];
        }
        else {
            return $request->getCookieParams()['JWT_AUTH_TOKEN'] ?? null;
        }
    }
}
