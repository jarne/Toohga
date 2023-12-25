<?php

/**
 * Toohga | main class
 */

namespace jarne\toohga;

use DI\Container;
use Dotenv\Dotenv;
use jarne\toohga\api\AdminController;
use jarne\toohga\api\APIController;
use jarne\toohga\storage\URLStorage;
use jarne\toohga\storage\UserStorage;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Error\LoaderError;

class Toohga
{
    /**
     * @var App
     */
    private App $slimApp;

    /**
     * Toohga constructor.
     *
     * @param App $slimApp
     */
    public function __construct(App $slimApp)
    {
        $this->slimApp = $slimApp;

        if (class_exists("Dotenv\Dotenv") and file_exists(__DIR__ . "/../../../.env")) {
            $dotenv = Dotenv::createUnsafeImmutable(__DIR__ . "/../../..");
            $dotenv->load();
        }
    }

    /**
     * Initialize middleware components
     */
    public function initMiddleware(): void
    {
        try {
            $twig = Twig::create(
                __DIR__ . "/../../../templates",
                ["cache" => getenv("DEV_ENV") === false ? __DIR__ . "/../../../twigCache" : false]
            );
        } catch (LoaderError $e) {
            return;
        }

        $this->slimApp->add(TwigMiddleware::create($this->slimApp, $twig));
        $this->slimApp->addBodyParsingMiddleware();
    }

    /**
     * Initialize the logic containers
     */
    public function initPreContainers(): void
    {
        $container = $this->slimApp->getContainer();

        if (!($container instanceof Container)) {
            return;
        }

        $container->set("urlStorage", function () {
            return new URLStorage();
        });

        $container->set("userStorage", function () {
            return new UserStorage();
        });
    }

    /**
     * Initialize the routing controller containers
     */
    public function initContainers(): void
    {
        $container = $this->slimApp->getContainer();

        if (!($container instanceof Container)) {
            return;
        }

        $container->set(APIController::class, function (ContainerInterface $container) {
            $urlStorage = $container->get("urlStorage");
            $userStorage = $container->get("userStorage");

            return new APIController($urlStorage, $userStorage);
        });
        $container->set(AdminController::class, function (ContainerInterface $container) {
            $urlStorage = $container->get("urlStorage");
            $userStorage = $container->get("userStorage");

            return new AdminController($urlStorage, $userStorage);
        });
    }

    /**
     * Initialize the URL routes
     */
    public function initRoutes(): void
    {
        $this->slimApp->get("/", APIController::class . ":home");
        $this->slimApp->post("/api/create", APIController::class . ":create");

        $this->slimApp->get("/privacy", APIController::class . ":privacy");

        $this->slimApp->get("/admin", AdminController::class . ":panel");

        $this->slimApp->post("/admin/api/auth", AdminController::class . ":authenticate");
        $this->slimApp->get("/admin/api/url", AdminController::class . ":getUrlList");
        $this->slimApp->delete("/admin/api/url/{urlId}", AdminController::class . ":deleteUrl");
        $this->slimApp->post("/admin/api/urlCleanup", AdminController::class . ":cleanupUrls");

        $this->slimApp->get("/admin/api/user", AdminController::class . ":getUserList");
        $this->slimApp->post("/admin/api/user", AdminController::class . ":createUser");
        $this->slimApp->delete("/admin/api/user/{userId}", AdminController::class . ":deleteUser");

        $this->slimApp->get("/{urlId}", APIController::class . ":get");
    }

    /**
     * @return App
     */
    public function getSlimApp(): App
    {
        return $this->slimApp;
    }
}
