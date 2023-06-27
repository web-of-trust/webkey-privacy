<?php

namespace App\Tests\Controller;

use App\Tests\TestCase;
use App\Controller\BaseController;
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface,
};
use Psr\Log\LoggerInterface;

class ControllerTest extends TestCase
{
    public function testBaseController()
    {
        $app = $this->getAppInstance();
        $container = $app->getContainer();
        $logger = $container->get(LoggerInterface::class);
        $testController = new class($logger) extends BaseController {
            public function __construct(
                LoggerInterface $logger
            ) {
                parent::__construct($logger);
            }

            protected function action(
                ServerRequestInterface $request,
                ResponseInterface $response,
                array $args
            ): ResponseInterface
            {
                $response->getBody()->write('test controller');
                return $response->withStatus(200);
            }
        };

        $app->get('/test-controller', $testController);
        $request = $this->createRequest('GET', '/test-controller');
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $this->assertEquals('test controller', $payload);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
