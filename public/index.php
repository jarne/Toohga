<?php
/**
 * Toohga | loader file
 */

require "../vendor/autoload.php";

use DI\Container;
use jarne\toohga\Toohga;
use Slim\Factory\AppFactory;

$container = new Container();
AppFactory::setContainer($container);

$app = AppFactory::create();
$toohga = new Toohga($app);

$toohga->initMiddleware();
$toohga->initPreContainers();
$toohga->initContainers();
$toohga->initRoutes();

$app->run();
