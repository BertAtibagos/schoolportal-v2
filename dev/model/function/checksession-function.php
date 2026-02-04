<?php 
	session_start();
	if(!isset($_SESSION["NAME"]) || !isset($_SESSION["ID"])){
		header("Location: ../Login.php");
		exit();
	} else {
        header("Location: ../Home.php");
        exit();
	}
?>