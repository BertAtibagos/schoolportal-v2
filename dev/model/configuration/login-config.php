<?php
	error_reporting(E_ALL & ~ E_NOTICE);
	
	require_once('connection-config.php');

	// Admin login form 
	// if(isset($_POST['submit']))
	// {
	// 	// Assigning POST values to variables.
	// 	$username = $_POST['username'];
	// 	$password = $_POST['password'];

	// 	// CHECK FOR THE RECORD FROM TABLE
	// 	$query = "SELECT * FROM fcpc_school_portal WHERE systemuser = '$sysuser_username'";
 	// 	$result = mysqli_query($dbconn, $query) or die(mysqli_error($dbconn));

	// 	$count = mysqli_num_rows($result);

	// 	if ($count > 0)
	// 	{
	// 		while($row = mysqli_fetch_array($result))
	// 		{

	// 			if($row['sysuser_password'] == md5($password))
	// 			{
	// 				if($row['sysuser_status'] == 1)
	// 				{
						

	// 					$url = 'https://' . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) ."/".$dashboard;
	// 					echo $url;
	// 					// header('Location: ' . $url);
	// 				}
	// 				else
	// 				{
	// 					echo "<script type='text/javascript'>alert(' User Account is not Active. Pls Contact the Admin.')</script>";
	// 					echo "<script type='text/javascript'>location.href='adminlogin.php'</script>";		
	// 				}
	// 			}
	// 			if($row['schlusr_password'] == $password)
	// 			{
	// 				$id = $row['schlusr_password'];
	// 				echo "<script type='text/javascript'>alert(' Redirecting to Changing Password.')</script>";
	// 				echo "<script type='text/javascript'>location.href='Pass_Change.php?id=$id'</script>";		
	// 			}

	// 			else
	// 			{
	// 				echo "<script type='text/javascript'>alert('Incorrect Username & Password Combination')</script>";
	// 			}
	// 		}
	// 	}	
       
	// }	
	if(isset($_POST['submit']))
	{
		$username = $_POST['username'];
		$password = $_POST['password'];

		echo $username .'-'. $password;

	}


?>