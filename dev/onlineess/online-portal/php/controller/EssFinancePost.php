
<?php 
	
	session_start();

	include '../configuration/connection-config.php';

	$response = array( 
		'status' => 0, 
		'message' => ''
	); 

    if ($_POST['type'] == 'POST_ESS_STAFF_ASSIGNED')
	{
		$ess_id 	= $_POST['ess_id'];
		$reg_id 	= $_POST['reg_id'];

		$qry = "	UPDATE 	`schoolenrollmentregistration` 
					SET 	`SchlEnroll_Assigned_ESS` = " . $ess_id  . "
					WHERE 	`SchlEnrollReg_ID` = " . $reg_id;

		$rsreg = $dbPortal->query($qry);
	}

    if ($_POST['type'] == 'POST_VERIFY_PAYMENT')
	{
		$qry = "UPDATE 	`oc_enrollment_payments` 

				SET 	`payment_status` = 1

				WHERE 	`id` = " . $_POST['pay_id'];

        $dbPortal->begin_transaction();  // this does the autocommit = false as well

        $dbPortal->query($qry);

        if ($dbPortal->commit()) {
            
            $response['status'] = 1;
            $response['message'] = 'PAYMENT IS VERIFIED';

        } else {

            $response['message'] = 'PAYMENT IS NOT VERIFIED';
            $dbPortal->rollback(); 	// Rollback transaction
        }
	}

	if ($_POST['type'] == 'POST_PAYMENT_CANCEL'){
            
        $payment_id = $_POST['payment_id'];

        $qry = "UPDATE `oc_enrollment_payments`

                SET    `payment_isCancel` = 1
                
                WHERE   `id` = $payment_id";
        
        $dbPortal->query($qry);	
		$count = mysqli_affected_rows($dbPortal);

		if ($count > 0){

			$response['status'] = 1;
            $response["message"] = "success";

		}else{

            $response["message"] = "error";
			
		}

    }

    if ($_POST['type'] == 'POST_PAYMENT_UNCANCEL'){
            
        $payment_id = $_POST['payment_id'];

        $qry = "UPDATE `oc_enrollment_payments`

                SET    `payment_isCancel` = 0
                
                WHERE   `id` = $payment_id";
        
        $dbPortal->query($qry);	
		$count = mysqli_affected_rows($dbPortal);

		if ($count > 0){

			$response['status'] = 1;
            $response["message"] = "success";

		}else{

            $response["message"] = "error";
			
		}

    }



    $dbPortal->close();
	echo json_encode($response);
?> 