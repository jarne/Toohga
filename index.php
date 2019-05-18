<?php
/**
 * Toohga | loader file
 */

require "vendor/autoload.php";

use jarne\toohga\Toohga;

$toohga = new Toohga();

echo $toohga->process($_SERVER, $_POST);
