<?php 
	
	session_start();

	include '../configuration/connection-config.php';

	date_default_timezone_set('Asia/Manila'); // CONFIGURE DATETIME TO SET IN PHILIPPINE TIME ZONE


	$response = array( 
		'status' => 0, 
		'message' => ''
	); 

	if ($_POST['type'] == 'POST_ESC_SHS_VOUCHER')
	{	
		$id 			= $_POST['id'];
		$reg_type 		= $_POST['reg_type'];
		$esc_voucher 	= $_POST['esc_voucher'];

		if($reg_type == "NEW"){

			$qry = "UPDATE `oc_user_accounts` 

					SET `esc_or_shs` =  $esc_voucher

					WHERE `id` = $id ";

		} elseif($reg_type == "OLD"){

			$qry = "UPDATE `schoolenrollmentregistration`

					SET `esc_or_shs` = $esc_voucher 

					WHERE (CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)
		
								WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
								ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)
								
							END) = $id ";

		}

		$rsreg = $dbPortal->query($qry);
	}

	if ($_POST['type'] == 'POST_ESS_STAFF_ASSIGNED')
	{
		$ess_id 	= $_POST['ess_id'];
		$reg_id 	= $_POST['reg_id'];

		$qry = "	UPDATE 	`schoolenrollmentregistration` `schl_enr_reg`
					SET 	`SchlEnroll_Assigned_ESS` = $ess_id
					WHERE 	(CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)
		
								WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
								ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)
								
							END) =  $reg_id ";

		$rsreg = $dbPortal->query($qry);
	}

	if ($_POST['type'] == 'POST_ASSESSMENT')
	{

		include '../configuration/connection-config.php';

		// VARIABLE FROM JAVASCRIPT

		$reg_id    = $_POST['reg_id'];
		$reg_type  = $_POST['reg_type'];
		$oc_acc_id = $_POST['oc_acc_id'];
		$lvlid     = $_POST['lvlid'];

		// VARIABLE FROM SESSION

		$lname = $_SESSION['lastname'];
		$fname = $_SESSION['firstname'];

		$date = date("Y-m-d H:i:s");


		$qry_get_sys_user_id = "SELECT 	
										`sys_user`.`SysUserSms_ID` `SYS_USER_ID`
						
								FROM 	`schoolemployee` `schl_emp`
								
									LEFT JOIN `systemuser` `sys_user`
										ON `schl_emp`.`SchlEmpSms_ID` = `sys_user`.`SchlUser_ID`
								
								WHERE 	`schl_emp`.`SchlEmp_LNAME` LIKE '%$lname%' AND 
										`schl_emp`.`SchlEmp_FNAME` LIKE '%$fname%' AND 
										`sys_user`.`SysUserType_ID` = 1 
									";

		$rsreg = $dbPortal->query($qry_get_sys_user_id);
		$fetch = $rsreg->fetch_assoc();

		$sys_user_id = $fetch['SYS_USER_ID'];
		
		if($lvlid == 3){

			// $qry_adm = "UPDATE 	`schoolenrollmentadmission` 

			// 		SET 	`SchlEnrollAdm_STATUS` 	= 1, 
			// 				`SysUser_ID`			= $sys_user_id,
			// 				`SchlEnrollAdmColl_DATETIME_UPDATE`	= '$date'

			// 		WHERE 	`SchlEnrollReg_ID` =  " .  $_POST['reg_id'];

			// $dbPortal->query($qry_adm);

			$qry_get_asmt_id = "SELECT 
										`schl_acad_asmt`.`SchlEnrollAss_ID` `ASMT_ID`
										
								FROM 	`schoolenrollmentregistration` `schl_enr_reg`
								
									LEFT JOIN `schoolenrollmentadmission` `schl_enr_adm`
										
										ON (CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)
								
											WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
											ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)
								
										END) = `schl_enr_adm`.`SchlEnrollReg_ID`
										
									LEFT JOIN `schoolenrollmentassessment` `schl_acad_asmt` -- FOR ASSESSMENT
															
										ON `schl_acad_asmt`.`SchlEnrollAdm_ID` = 		
															(CASE IFNULL(`schl_enr_adm`.`SchlEnrollAdmSms_ID`, 0)
																		
																WHEN 0 THEN IFNULL(`schl_enr_adm`.`SchlEnrollAdm_ID`, 0)
																WHEN 1 THEN IFNULL(`schl_enr_adm`.`SchlEnrollAdm_ID`, 0)
								
																ELSE IFNULL(`schl_enr_adm`.`SchlEnrollAdmSms_ID`, 0)
								
															END)
															
								WHERE 	(CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)
								
											WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
											ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)
											
										END) = $reg_id";

			$rsreg = $dbPortal->query($qry_get_asmt_id);
			$fetch = $rsreg->fetch_assoc();
			$asmt_id = $fetch['ASMT_ID'];

			$update_asmt = "UPDATE 	`schoolenrollmentassessment`
		
							SET		`SysUser_ID` =  $sys_user_id,
									`SchlEnrollAss_DATETIME_UPDATE` = '$date'
								
							WHERE 	`SchlEnrollAss_ID` = $asmt_id";

			//$response['message'] = $update_asmt;


			$dbPortal->query($update_asmt);

			if($_POST['reg_type'] == 'OLD'){

				$qry_reg = "UPDATE 	`schoolenrollmentregistration` 

							SET 	`oc_reg_status` = 2

							WHERE 	`SchlEnrollReg_ID` = $reg_id";

				$dbPortal->query($qry_reg);

				$qry_adm = "UPDATE 	`schoolenrollmentadmission` 

							SET 	`SchlEnrollAdm_STATUS` = 1,
									`SysUser_ID` = $sys_user_id,
									`SchlEnrollAdmColl_DATETIME_UPDATE`	= '$date'
							

							WHERE `SchlEnrollReg_ID` =  $reg_id ";

				$dbPortal->query($qry_adm);
				$count = mysqli_affected_rows($dbPortal);
		
				if ($count > 0){

					$response['status'] = 1;
					$response['message'] = 'STUDENT ASSESSMENT IS VERIFIED';

				} else {

					$response['message'] = 'VERIFICATION IS NOT SUCCESSFUL';
					$response['status'] = 0;

				}

			} else if($_POST['reg_type'] == 'NEW'){ 


				$qry_occ = "UPDATE 	`oc_user_accounts` 

							SET 	`is_assessed` 	= 1 ,
									`oc_reg_status` = 2

							WHERE 	`id` = $oc_acc_id ";

				$dbPortal->query($qry_occ);

				$qry_adm = "UPDATE `schoolenrollmentadmission` 

							SET `SchlEnrollAdm_STATUS` = 1 

							WHERE `SchlEnrollReg_ID` =  $reg_id";

				$dbPortal->query($qry_adm);

				$count = mysqli_affected_rows($dbPortal);

				if ($count > 0){

					$response['status'] = 1;
					$response['message'] = 'STUDENT ASSESSMENT IS VERIFIED';

				} else {

					$response['message'] = 'VERIFICATION IS NOT SUCCESSFUL';
					$response['status'] = 0;

				}
			}
		
		} else {

			if($_POST['reg_type'] == 'OLD'){

				$qry_adm = "UPDATE 	`schoolenrollmentadmission` 

							SET 	`SchlEnrollAdm_STATUS` = 1, 
									`SysUser_ID` = $sys_user_id,
									`SchlEnrollAdmColl_DATETIME_UPDATE`	= '$date'

							WHERE 	`SchlEnrollReg_ID` =  $reg_id ";

				$dbPortal->begin_transaction();  // this does the autocommit = false as well

				$dbPortal->query($qry_adm);

				if ($dbPortal->commit()) {
					
					$qry_adm = "UPDATE 	`schoolenrollmentregistration` 

								SET 	`oc_reg_status` = 2

								WHERE 	`SchlEnrollReg_ID` =  $reg_id ";
						
					if ($dbPortal->commit()) {

						$response['status'] = 1;
						$response['message'] = 'STUDENT ASSESSMENT IS VERIFIED';	

					} else {

						$response['message'] = 'VERIFICATION IS NOT SUCCESSFUL';
						$dbPortal->rollback(); 	// Rollback transaction

					}

				} else {

					$response['message'] = 'VERIFICATION IS NOT SUCCESSFUL';
					$dbPortal->rollback(); 	// Rollback transaction
				}


			} else if($_POST['reg_type'] == 'NEW'){

				$qry_occ = "UPDATE 	`oc_user_accounts` 

							SET 	`is_assessed` 	= 1 ,
									`oc_reg_status` = 2

							WHERE 	`id` = " .  $_POST['oc_acc_id'];

				$qry_adm = "UPDATE 	`schoolenrollmentadmission` 

							SET 	`SchlEnrollAdm_STATUS` = 1,
									`SysUser_ID` = $sys_user_id,
									`SchlEnrollAdmColl_DATETIME_UPDATE`	= '$date'
							

							WHERE `SchlEnrollReg_ID` =  $reg_id ";

				$dbPortal->begin_transaction();  // this does the autocommit = false as well

				$dbPortal->query($qry_occ);
				$dbPortal->query($qry_adm);

				if ($dbPortal->commit()) {
					
					$response['status'] = 1;
					$response['message'] = 'STUDENT ASSESSMENT IS VERIFIED';

				} else {

					$response['message'] = 'VERIFICATION IS NOT SUCCESSFUL';
					$dbPortal->rollback(); 	// Rollback transaction

				}

			}

			// $response['message'] = $qry;
			
		}
		

		
	}

	

	if ($_POST['type'] == 'POST_CANCEL_ADMISSION')
	{
		include '../configuration/connection-config.php';
		
		$date = date("Y-m-d H:i:s");
		$lname = $_SESSION['lastname'];
		$fname = $_SESSION['firstname'];

		$qry_get_sys_user_id = "SELECT 	
										`sys_user`.`SysUserSms_ID` `SYS_USER_ID`
						
								FROM 	`schoolemployee` `schl_emp`
								
									LEFT JOIN `systemuser` `sys_user`
										ON `schl_emp`.`SchlEmpSms_ID` = `sys_user`.`SchlUser_ID`
								
								WHERE 	`schl_emp`.`SchlEmp_LNAME` LIKE '%$lname%' AND 
										`schl_emp`.`SchlEmp_FNAME` LIKE '%$fname%' AND 
										`sys_user`.`SysUserType_ID` = 1 
									";

		$rsreg = $dbPortal->query($qry_get_sys_user_id);
		$fetch = $rsreg->fetch_assoc();

		$sys_user_id = $fetch['SYS_USER_ID'];

		$reg_id = $_POST['reg_id'];
		$adm_id = $_POST['adm_id'];

		$qry_adm = "UPDATE `schoolenrollmentadmission`
					SET `SchlEnrollAdm_STATUS` = 0
					WHERE 
						(CASE 
							WHEN IFNULL(`SchlEnrollAdmSms_ID`, 0) = 0 
							THEN IFNULL(`SchlEnrollAdm_ID`, 0)
							ELSE `SchlEnrollAdmSms_ID`
						END) = $adm_id";

		$dbPortal->query($qry_adm);

		$qry_reg = "UPDATE 	`schoolenrollmentregistration` 

				SET 	`SchlEnrollReg_STATUS` = 0 
				
				WHERE 	(CASE IFNULL(`SchlEnrollRegSms_ID`, 0)
				
						WHEN 0 THEN IFNULL(`SchlEnrollReg_ID`, 0)
						ELSE IFNULL(`SchlEnrollRegSms_ID`, 0)
					END) = $reg_id ";

		$dbPortal->query($qry_reg);

		$count = mysqli_affected_rows($dbPortal);
		
		if ($count > 0){

			$response['message'] = 'Admission is Cancelled.';
			$response['status'] = 1;

		} else {

			$response['message'] = 'There is an Error on Cancellation, Click again.';
			$response['status'] = 0;

		}

		
	}





	$dbPortal->close();
	echo json_encode($response);
?>