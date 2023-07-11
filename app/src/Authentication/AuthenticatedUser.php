<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Authentication;

/**
 * Authenticated user class
 * 
 * @package  App
 * @category Authentication
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class AuthenticatedUser implements UserInterface
{
    /**
     * Constructor
     */
    public function __construct(
        private readonly string $identity = '',
        private readonly array $roles = [],
        private readonly array $details = []
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentity(): string
    {
        return $this->identity;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * {@inheritdoc}
     */
    public function getDetail(string $name, $default = NULL)
    {
        return $this->details[$name] ?? $default;
    }
}
