<?php 
	require '../configuration/connection-config.php';

	$response = array( 
		'status' => 0, 
		'message' => 'Login failed, please try again.',
		'firstname' => 'firstname',
		'lastname' => 'lastname'

	); 
	if(isset($_POST['uname']) != null && isset($_POST['upass']) != null)
	{
		$username	= $_POST['uname'];
		$password 	= $_POST['upass'];
																							
		$qry = "SELECT * FROM `oc_school_users` WHERE `schlusr_username` = '" . $username . "'";
		$row = mysqli_query($dbPortal, $qry) or die(mysqli_error($dbPortal));

		$result = mysqli_fetch_array($row);

		if($result != null && ($result['schlusr_password'] == md5($password))){

			session_start();
			$response['status'] = 1;
			$response['message'] = 'Login Successful!';
			$response['firstname'] = $result["schlusr_fname"];
			$response['lastname'] = $result["schlusr_lname"];


			$_SESSION['username']   = $result["schlusr_username"];
			$_SESSION['firstname']  = $result["schlusr_fname"];
			$_SESSION['lastname']	= $result["schlusr_lname"];
			$_SESSION['acclvl']		= $result["schlusr_acclvl"];
			$_SESSION['user_id']	= $result["schlusr_id"];
			


		} else {

			$response['message'] = 'Incorrect Password, Please Try Again.';		
		}
	}

	mysqli_close($dbPortal);
	echo json_encode($response);

?>