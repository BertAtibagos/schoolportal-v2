<?php 

require_once('../../configuration/connection-config.php');
if (isset($_POST['userid']) && isset($_POST['usertype']) && !empty($_POST['old_pass']) && !empty($_POST['new_pass']) && !empty($_POST['conf_new_pass'])) {

	$old_pass = $_POST['old_pass'];

	$new_pass = $_POST['new_pass'];

	$conf_new_pass = $_POST['conf_new_pass'];

	$userid = $_POST['userid'];
	$usertype = $_POST['usertype'];


	if($new_pass != $conf_new_pass){
		echo 'New Password and Confirm New Password does not match!!';
	}
	
	if($new_pass == $conf_new_pass){
		$sql = "SELECT `user`.`sysuser_password` `PASS` , count(*) `count`, `type`.`sysusertype_name`  `type` 
					FROM systemuser `user`
					LEFT JOIN `systemusertype` `type`
					ON `user`.`sysusertype_id` = `type`.`sysusertypesms_id`
					WHERE `user`.`sysusersms_id` = ".$_POST['userid']." AND `user`.`sysuser_password` = MD5('".$_POST['old_pass']."') AND `type`.`sysusertype_name` = '$usertype'";
		$rsreg = $dbConn->query($sql);
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);

		foreach($fetch as $regitem){
			$fetchcount = $regitem['count'];
		}

		if($fetchcount == 1){
			$sql = "UPDATE systemuser `user` 
			SET `user`.`sysuser_password` =  MD5('".$_POST['new_pass']."')
			WHERE `user`.`sysusersms_id` = ".$_POST['userid']." 
				AND `user`.`sysuser_password` =  MD5('".$_POST['old_pass']."')";
			$rsreg = $dbConn->query($sql);
			
			session_start();
			clearstatcache();
			session_destroy();
			echo '<script>window.location.reload();</script>';
		}
		else if ($fetchcount == 0){
			echo '<script>
					$("#messcont").css("border", "#ff7171 solid 3px");
					$("#messcont").css("border-radius", "10px");
					$("#messcont").css("background-color", "#ff505030")
					$("#messcont").css("padding", "5px");
				</script>';
			echo 'ERROR: You have enterred the wrong password!';
		} else {//if there 2 or more accounts got
			echo '<script>
					$("#messcont").css("border", "#ff7171 solid 3px");
					$("#messcont").css("border-radius", "10px");
					$("#messcont").css("background-color", "#ff505030")
					$("#messcont").css("padding", "5px");
				</script>';
			echo 'MULTIPLE ACCOUNTS DETECTED: Please contact the ICT DEPARTMENT!';
		}

	}

	
}