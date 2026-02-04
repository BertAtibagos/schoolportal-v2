
<?php 
	include '../configuration/connection-config.php';

	session_start();

	if ($_GET['type'] == 'GET_ACCESS_LEVEL')
	{	
		$acc_lvl = $_SESSION['acclvl'];

		$ids = explode(",", $acc_lvl);
		
		$fetch = $ids;
	}

	$dbPortal->close();
	echo json_encode($fetch);
?>