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
    public const USERNAME = 'username';
    public const PASSWORD = 'password';

    /**
     * {@inheritdoc}
     */
    public function authenticate(
        ServerRequestInterface $request
    ): ?UserInterface
    {
        $parsedBody = $request->getParsedBody();
        if (is_array($parsedBody)) {
            $username = filter_var(
                $parsedBody[self::USERNAME] ?? '',
                FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            );
            $password = $parsedBody[self::PASSWORD] ?? '';
            $user = $this->getUserEntity($username);
            if ($user instanceof UserEntity &&
                password_verify($password, $user->getPassword())) {
                return $this->dtoUserEntity($user);
            }
        }
        return null;
    }
}
