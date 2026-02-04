<?php
	session_start();

	// Define the bypass code (can be stored securely elsewhere)
	$maintenanceBypassCode = '011000';

	// Check if maintenance mode is enabled
	$_SESSION['maintenance'] = 0;

	if (!empty($_SESSION['maintenance']) && $_SESSION['maintenance'] === 1) {

		// Allow bypass if correct code is in the URL
		if (isset($_GET['force']) && $_GET['force'] === $maintenanceBypassCode) {
			// Set session flag to allow bypass on future requests
			$_SESSION['bypass_maintenance'] = true;
		}

		// Redirect to maintenance page if not bypassed
		if (empty($_SESSION['bypass_maintenance'])) {
			session_destroy();
			header('Location: https://schoolportal.fcpc.edu.ph/maintenance.php');
			// header('Location: http://localhost/schoolportal/maintenance.php');
			exit("Site on maintenance");
		}
	}

	if (isset($_SESSION['USERID'])) {
		header("Location: model/forms/masterpage-model.php");
		exit();
	} else {
		//session_destroy();
		clearstatcache();
        header("Location: model/login-model.php");
		exit();
	}
?>