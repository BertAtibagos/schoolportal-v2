		
<?php  
	require("configuration-config.php");

	$dbPortal = new mysqli($servername2, $serverusername2, $serverpassword2, $serverdb2);

	// Check connection
	if ($dbPortal->connect_error) {
		die("Connection failed: " . $connection->connect_error);
	}

	// Set character set to avoid charset issues
	$dbPortal->set_charset('utf8mb4');

	// Set SQL mode to match SQLyog
	// $dbPortal->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION'");


?>  