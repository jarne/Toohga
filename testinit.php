<?php
/**
 * Toohga | DB test data init script
 */

require "./dbconn.php";

if (!isset($mysqli)) {
    return;
}

$query = file_get_contents("testdata.sql");

if (!$query) {
    echo "Couldn't read SQL test data file!";

    return;
}

try {
    $res = $mysqli->multi_query($query);

    if (!$res) {
        echo "SQL test data creation query wasn't executed successfully!";

        return;
    }
} catch (mysqli_sql_exception $sqlExc) {
    echo "SQL test data creation query failed: " . $sqlExc->getMessage();

    return;
}
