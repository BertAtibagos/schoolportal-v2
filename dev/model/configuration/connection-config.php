<?php  
	// require_once("configuration-config.php");
	// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	// ini_set('display_errors', 1);
	// $dbConn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	// $dbConn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    // if (!$dbConn) {
    //    die("Connection failed: " . mysqli_connect_error());
    // }

	require_once("configuration-config.php");
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	ini_set('display_errors', 1);
	$dbConn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
	$dbConn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
	$dbConn->set_charset("utf8");
    if (!$dbConn) {
       die("Connection failed: " . mysqli_connect_error());
    } else {
		// echo 'connected';
	}
?>