<?php declare(strict_types=1);

namespace App\Authentication;

/**
 * Default user class
 * 
 * @package  App
 * @category Authentication
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class DefaultUser implements UserInterface
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
