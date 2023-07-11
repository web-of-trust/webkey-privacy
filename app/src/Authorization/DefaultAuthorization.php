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

/**
 * Default authorization class
 * 
 * @package  App
 * @category Authorization
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class DefaultAuthorization implements AuthorizationInterface
{
    /**
     * Constructor
     *
     * @param array $rules
     * @return self
     */
    public function __construct(
        private readonly array $rules
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
            $uri = preg_replace(
                '#/+#', '/', '/' . $request->getUri()->getPath()
            );
            $paths = $this->rules[$role->name] ?? [];
            if (is_array($paths)) {
                foreach ($paths as $path) {
                    $path = rtrim($path, '/');
                    if (!!preg_match("@^{$path}(/.*)?$@", $uri)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
