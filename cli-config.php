<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 09.03.17
 * Time: 13:19
 */

require "vendor/autoload.php";

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use jarne\toohga\Toohga;

$toohga = new Toohga();
$entityManager = $toohga->getEntityManager();

return ConsoleRunner::createHelperSet($entityManager);