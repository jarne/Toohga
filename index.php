<?php
/**
 * Toohga | loader file
 */

require "vendor/autoload.php";

use jarne\toohga\Toohga;
use Klein\Klein;

$klein = new Klein();
$toohga = new Toohga($klein);

$toohga->initRoutes();

$klein->dispatch();
