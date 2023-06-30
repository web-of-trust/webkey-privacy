<?php declare(strict_types=1);

namespace App\Authentication;

use App\Entity\UserEntity;
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface,
};

/**
 * Default authentication class
 * 
 * @package  App
 * @category Authentication
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class DefaultAuthentication extends BaseAuthentication
{
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
                $parsedBody['username'] ?? '',
                FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            );
            $password = $parsedBody['password'] ?? '';
            $user = $this->getUserEntity($username);
            if ($user instanceof UserEntity &&
                password_verify($password, $user->getPassword())) {
                return $this->dtoUserEntity($user);
            }
        }
        return null;
    }
}
