<?php 
	session_start();
	
	unset($_SESSION['username']);
	unset($_SESSION['firstname']);
	unset($_SESSION['lastname']);
	unset($_SESSION['acclvl']);
	unset($_SESSION['user_id']);

	session_destroy();
	header('location: ../../login.php');
?>