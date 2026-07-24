<?php
    require('conn.php');

    $dbConn = new mysqli($servername, $serverusername, $serverpassword, $serverdb, $serverport);

    if ($dbConn->connect_error) {
        die("Connection Failed: " . $dbConn->connect_error);
    }

    $dbConn->set_charset('utf8mb4');
?>