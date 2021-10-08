<?php
/**
 * Toohga | DB schema init script
 */

require "./dbconn.php";

if (!isset($mysqli)) {
    return;
}

$query = file_get_contents("schema.sql");

if (!$query) {
    echo "Couldn't read SQL schema file!";

    return;
}

try {
    $res = $mysqli->multi_query($query);

    if (!$res) {
        echo "SQL schema creation query wasn't executed successfully!";

        return;
    }

    echo "SQL schema successfully initialized!";
} catch (mysqli_sql_exception $sqlExc) {
    echo "SQL schema creation query failed: " . $sqlExc->getMessage();

    return;
}
