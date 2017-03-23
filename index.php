<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 08.03.17
 * Time: 20:23
 */

require "vendor/autoload.php";

use jarne\toohga\Toohga;

$toohga = new Toohga();

echo $toohga->process($_SERVER, $_POST);