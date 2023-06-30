<?php declare(strict_types=1);

namespace App\Authentication;

use App\Entity\UserEntity;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface,
};

/**
 * Token authentication class
 * 
 * @package  App
 * @category Authentication
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class TokenAuthentication extends BaseAuthentication
{
    /**
     * Constructor
     *
     * @param EntityManagerInterface $entityManager
     * @param TokenRepositoryInterface $tokenRepository
     * @return self
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        private readonly TokenRepositoryInterface $tokenRepository
    )
    {
        parent::__construct($entityManager);
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(
        ServerRequestInterface $request
    ): ?UserInterface
    {
        $token = $this->tokenRepository->load(
            self::getAuthToken($request)
        );
        if ($token instanceof TokenInterface) {
            $payload = $token->getPayload();
            $uid = $payload['uid'] ?? '';
            $user = $this->getUserEntity($uid);
            if ($user instanceof UserEntity) {
                return $this->dtoUserEntity($user);
            }
        }
        return null;
    }

    /**
     * Get auth token from request
     * 
     * @param ServerRequestInterface $request
     * @return string
     */
    private static function getAuthToken(ServerRequestInterface $request): string
    {
        $header = $request->getHeaderLine(
            TokenRepositoryInterface::TOKEN_HEADER
        );
        if (!empty($header) &&
            preg_match(TokenRepositoryInterface::TOKEN_PATTERN, $header, $matches)) {
            return $matches[1];
        }
        else {
            $params = $request->getCookieParams();
            return $params[TokenRepositoryInterface::TOKEN_COOKIE] ?? '';
        }
    }
}
