<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Authorization;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

/**
 * Access control list authorization class
 * 
 * @package  App
 * @category Authorization
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class AclAuthorization implements AuthorizationInterface
{
    /**
     * Constructor
     *
     * @param array $acl access control list
     * @return self
     */
    public function __construct(
        private readonly array $acl
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted(Role $role, ServerRequestInterface $request): bool
    {
        if ($role instanceof Role::Administrator) {
            return true;
        }
        elseif ($role instanceof Role::AuthenticatedUser) {
            $routeName = RouteContext::fromRequest($request)->getRoute()?->getName();
            if (!empty($routeName)) {
                $resources = $this->acl[$role->name] ?? [];
                if (is_array($resources)) {
                    foreach ($resources as $resource) {
                        if ($resource === $routeName) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }
}
