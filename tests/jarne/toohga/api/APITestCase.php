<?php

/**
 * Toohga | API controller test case
 */

namespace jarne\toohga\tests\api;

use DI\Container;
use jarne\toohga\tests\storage\URLStorageMock;
use jarne\toohga\tests\storage\UserStorageMock;
use jarne\toohga\Toohga;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

class APITestCase extends TestCase
{
    /**
     * @var Toohga
     */
    protected Toohga $toohga;

    /**
     * Setup testing environment
     */
    protected function setUp(): void
    {
        $container = new Container();
        AppFactory::setContainer($container);

        $app = AppFactory::create();
        $this->toohga = new Toohga($app);

        $this->toohga->initMiddleware();
        $this->toohga->initPreContainers();
        $this->initTestPreContainers();
        $this->toohga->initContainers();
        $this->toohga->initRoutes();
    }

    /**
     * Initialize logic containers for testing
     */
    private function initTestPreContainers(): void
    {
        $container = $this->toohga->getSlimApp()->getContainer();

        if (!($container instanceof Container)) {
            return;
        }

        $container->set("urlStorage", function () {
            return new URLStorageMock();
        });

        $container->set("userStorage", function () {
            return new UserStorageMock();
        });
    }

    /**
     * Create an API request
     *
     * @param string $method
     * @param string $uri
     * @param array $serverParams
     *
     * @return ServerRequestInterface
     */
    public function request(string $method, string $uri, array $serverParams = array()): ServerRequestInterface
    {
        $serverRequestFactory = new ServerRequestFactory();

        return $serverRequestFactory->createServerRequest($method, $uri, $serverParams);
    }

    /**
     * @return App
     */
    #[Pure] protected function getApp(): App
    {
        return $this->toohga->getSlimApp();
    }
}
