<?php declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface,
};

/**
 * Login controller class
 * 
 * @package  App
 * @category Controller
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class LoginController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    protected function action(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface
    {
        return $response;
    }
}
