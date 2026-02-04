<?php
	session_start();if (isset($_SESSION['account_id']))
	{
		header("Location: online-portal/php/partials/main-dashboard.php");
		exit();
	} else {
		session_destroy();
        header("Location: online-portal/login.php");
		exit();
	}
?>