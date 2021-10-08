<?php
/**
 * Toohga | DB connection script
 */

require "./vendor/autoload.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

use Dotenv\Dotenv;

if (class_exists("Dotenv\Dotenv") and file_exists(__DIR__ . "/.env")) {
    $dotenv = Dotenv::createUnsafeImmutable(__DIR__);
    $dotenv->load();
}

try {
    $mysqli = new mysqli(
        getenv("MYSQL_HOST"),
        getenv("MYSQL_USER"),
        getenv("MYSQL_PASSWORD"),
        getenv("MYSQL_DATABASE")
    );
} catch (mysqli_sql_exception $sqlExc) {
    echo "Connection to DB failed: " . $sqlExc->getMessage();

    return;
}
