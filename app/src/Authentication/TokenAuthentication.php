<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * @param TokenRepositoryInterface $tokenRepository
     * @param EntityManagerInterface $entityManager
     * @return self
     */
    public function __construct(
        private readonly TokenRepositoryInterface $tokenRepository,
        EntityManagerInterface $entityManager
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
            $uid = filter_var(
                $payload['uid'] ?? '',
                FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            );
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
