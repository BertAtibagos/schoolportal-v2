<?php
	session_start();
	//$_SESSION["USERACCESSRIGHTS"] = "mnu-academic,mnu-enrollment,mnu-subject,mnu-transaction,mnu-myaccount";
	echo $_SESSION["USERACCESSRIGHTS"];
?>