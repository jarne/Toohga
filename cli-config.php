<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 09.03.17
 * Time: 13:19
 */

require "vendor/autoload.php";

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use jarne\tooh\Toohga;

$tooh = new Toohga();
$entityManager = $tooh->getEntityManager();

return ConsoleRunner::createHelperSet($entityManager);