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
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface,
};

/**
 * Login authentication class
 * 
 * @package  App
 * @category Authentication
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class LoginAuthentication extends BaseAuthentication
{
    private const USERNAME_PARAM = 'username';
    private const PASSWORD_PARAM = 'password';

    /**
     * {@inheritdoc}
     */
    public function authenticate(
        ServerRequestInterface $request
    ): ?UserInterface
    {
        $parsedBody = $request->getParsedBody();
        if (is_array($parsedBody)) {
            $username = $parsedBody[self::USERNAME_PARAM] ?? null;
            $password = $parsedBody[self::PASSWORD_PARAM] ?? null;
        }
        else {
            $username = $request->getServerParams()['PHP_AUTH_USER'] ?? null;
            $password = $request->getServerParams()['PHP_AUTH_PW'] ?? null;
            if ($username === null || $password === null) {
                $header = $request->getHeaderLine(
                    TokenRepositoryInterface::AUTHORIZATION_HEADER
                );
                if (!empty($header) &&
                    preg_match(TokenRepositoryInterface::BASIC_TOKEN_PATTERN, $header, $matches)) {
                    list($username, $password) = array_map(
                        static fn ($value) => $value === '' ? null : $value,
                        explode(':', base64_decode($matches[1]), 2)
                    );
                }
            }
        }
        if ($username !== null && $password !== null) {
            $user = $this->getUserEntity(filter_var(
                $username,
                FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            ));
            if ($user instanceof UserEntity &&
                password_verify($password, $user->getPassword())) {
                return $this->dtoUserEntity($user);
            }
        }
        return null;
    }
}
