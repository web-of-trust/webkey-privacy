<?php declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Jwt authentication middleware class
 * 
 * @package  App
 * @category Middleware
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class JwtAuthentication extends BaseAuthentication
{
    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @return self
     */
    public function __construct(
        LoggerInterface $logger
    )
    {
        parent::__construct($logger);
    }

    /**
     * {@inheritdoc}
     */
    protected function validate(ServerRequestInterface $request): bool
    {
        return false;
    }
}
