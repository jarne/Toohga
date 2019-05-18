<?php
/**
 * Toohga | doctrine cli config
 */

require "vendor/autoload.php";

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use jarne\toohga\Toohga;

$toohga = new Toohga();
$entityManager = $toohga->getEntityManager();

return ConsoleRunner::createHelperSet($entityManager);
