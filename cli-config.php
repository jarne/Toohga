<?php
/**
 * Toohga | doctrine cli config
 */

require "vendor/autoload.php";

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;
use Dotenv\Dotenv;

if(class_exists("Dotenv\Dotenv") AND file_exists(__DIR__ . "/../../../.env")) {
    $dotenv = Dotenv::create(__DIR__ . "/../../..");
    $dotenv->load();
}

$credentials = array(
    "driver" => "pdo_mysql",
    "host" => getenv("MYSQL_HOST"),
    "user" => getenv("MYSQL_USER"),
    "password" => getenv("MYSQL_PASSWORD"),
    "dbname" => getenv("MYSQL_DATABASE"),
);

try {
    $entityManager = EntityManager::create(
        $credentials,
        Setup::createAnnotationMetadataConfiguration(array("src/jarne/toohga/entity"), false, null)
    );
} catch(ORMException $exception) {
    return null;
}

return ConsoleRunner::createHelperSet($entityManager);
