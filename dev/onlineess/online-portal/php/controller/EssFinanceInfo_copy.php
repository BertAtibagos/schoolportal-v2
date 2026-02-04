<?php 

	include '../configuration/connection-config.php';

	if ($_GET['type'] == 'GET_OPEN_YEARPERIOD') { // ***** GET ALL OPEN ACADEMIC PERIOD

		$qry = "SELECT 	
					`SchlAcadLvl_ID` `ACAD_LVL_ID`,
					`SchlAcadYr_ID` `ACAD_YR_ID`,
					`SchlAcadPrd_ID` `ACAD_PRD_ID`

				FROM	`schoolacademicyearperiod` 

				WHERE 	`SchlAcadYrPrd_ISOPEN` = 1 ";
		
		$rsreg = $dbPortal->query($qry);
		$fetch = $rsreg->fetch_assoc();
		$dbPortal->close();
	} 

	if ($_GET['type'] == 'GET_REGISTERED_STUDENTS') { // ***** GET ALL ASSESSED STUDENTS 

		$stud_name 		= $_GET['stud_name'];
		$stud_name_type = $_GET['stud_name_type'];
		$lvlid 			= $_GET['lvlid'];


		$qry_schl_acad_yrprd = "SELECT 	`SchlAcadLvl_ID` `ACAD_LVL_ID`,
										`SchlAcadYr_ID` `ACAD_YR_ID`,
										`SchlAcadPrd_ID` `ACAD_PRD_ID`

								FROM	`schoolacademicyearperiod` 

								WHERE 	`SchlAcadYrPrd_ISOPEN` = 1 AND 
										`SchlAcadLvl_ID` = $lvlid ";

		$rsreg = $dbPortal->query($qry_schl_acad_yrprd);
		$open_schl_acad_yrprd = $rsreg->fetch_ALL();

		$counter = count($open_schl_acad_yrprd);

		$stud_container = [];

		$fetch = null;
		
		for ($x=0; $x < $counter; $x++) { 

			$cont_open_schl_acad_yrprd = $open_schl_acad_yrprd[$x];

			foreach ($cont_open_schl_acad_yrprd as $value) {

				$YR_ID  = $cont_open_schl_acad_yrprd[1];
				$PRD_ID = $cont_open_schl_acad_yrprd[2];

			$qry = "SELECT 	DISTINCT
							`schl_enr_reg`.`SchlEnrollReg_ID` `REG_ID`,
							`schl_enr_reg`.`SchlEnrollReg_DATETIME` `REG_DATE`,
							UPPER(CONCAT(	`oc_enr_paymt`.`last_name`, ', ', 
											`oc_enr_paymt`.`first_name` , ' ', 
											`oc_enr_paymt`.`middle_name`)) `NAME`,
									
							`schl_enr_reg`.`SchlEnrollReg_TYPE` `REG_TYPE`,
							`schl_acad_lvl`.`SchlAcadLvl_NAME` `LVL_NAME`, 
							`schl_acad_yrlvl`.`SchlAcadYrLvl_NAME` `YRLVL_NAME`,
							`schl_enr_adm`.`SchlAcadPrd_ID` `PRD_ID`,
							`schl_acad_crses`.`SchlAcadCrses_NAME` `CRSE_NAME`,
							
							`schl_enr_reg`.`SchlEnrollReg_STATUS` `REG_STATUS`,
							`schl_enr_adm`.`SchlEnrollAdm_STATUS` `ADM_STATUS`,
							`schl_enr_asmt`.`SchlEnrollAss_STATUS` `ASMT_STATUS`,
						
							(
								SELECT 	DISTINCT COUNT(*) `COUNT` 
								FROM 	`oc_enrollment_payments` `oc_enr_pay` 
								WHERE 	`schl_enr_reg`.`SchlEnrollReg_ID` = `oc_enr_pay`.`registration_id` AND 
										`oc_enr_pay`.`payment_isCancel` = 0) `NO_PAYMENTS`

					FROM 	`oc_enrollment_payments` `oc_enr_paymt`

					LEFT JOIN `schoolenrollmentregistration` `schl_enr_reg`
						ON `oc_enr_paymt`.`registration_id` = `schl_enr_reg`.`SchlEnrollReg_ID`	
					
					LEFT JOIN `schoolenrollmentadmission` `schl_enr_adm`				
						ON (CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

							WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
							WHEN 1 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)

							ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

						END) = `schl_enr_adm`.`SchlEnrollReg_ID`
						
					LEFT JOIN `schoolenrollmentassessment` `schl_enr_asmt`
						ON `schl_enr_adm`.`SchlEnrollAdmSms_ID` = `schl_enr_asmt`.`SchlEnrollAdm_ID`
						
					LEFT JOIN  `schoolacademiclevel` `schl_acad_lvl`
						ON `schl_enr_reg`.`SchlAcadLvl_ID` = `schl_acad_lvl`.`SchlAcadLvlSms_ID`
						
					LEFT JOIN `schoolacademicyearlevel` `schl_acad_yrlvl`
						ON `schl_enr_adm`.`SchlAcadYrLvl_ID` = `schl_acad_yrlvl`.`SchlAcadYrLvlSms_ID`
						
					LEFT JOIN `schoolacademiccourses` `schl_acad_crses`
						ON `schl_enr_adm`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID`

					WHERE 							
							`schl_enr_reg`.`SchlAcadLvl_ID`  = $lvlid  AND 
							`schl_enr_adm`.`SchlAcadYr_ID`	 = $YR_ID AND 
							`schl_enr_adm`.`SchlAcadPrd_ID`  = $PRD_ID  AND 
									
							`schl_enr_reg`.`SchlEnrollReg_ID` != 0 AND 
							`schl_enr_reg`.`SchlEnrollReg_ID` != 1 AND 
							
							`schl_enr_adm`.`SchlEnrollAdm_STATUS` IS NOT NULL  AND 

							`oc_enr_paymt`.`$stud_name_type` LIKE '%$stud_name%' and 
							
							`schl_enr_adm`.`SchlEnrollAdm_STATUS` = 1
						
					ORDER BY `schl_enr_reg`.`SchlEnrollReg_DATETIME` DESC

					";

					$rsreg = $dbPortal->query($qry);
					$stud_container = $rsreg->fetch_ALL(MYSQLI_ASSOC);

			}		
		}
		
		$fetch = $stud_container;
		$dbPortal->close();
	}

	if ($_GET['type'] == 'GET_STUDENTS_WITHOUT_PAYMENTS') { // ***** GET ALL ASSESSED STUDENTS WITHOUT PAYMENTS 

		$stud_name 		= $_GET['stud_name'];
		$stud_name_type = $_GET['stud_name_type'];
		$lvlid 			= $_GET['lvlid'];


		$qry_schl_acad_yrprd = "SELECT 	`SchlAcadLvl_ID` `ACAD_LVL_ID`,
										`SchlAcadYr_ID` `ACAD_YR_ID`,
										`SchlAcadPrd_ID` `ACAD_PRD_ID`

								FROM	`schoolacademicyearperiod` 

								WHERE 	`SchlAcadYrPrd_ISOPEN` = 1 AND 
										`SchlAcadLvl_ID` = $lvlid ";

		$rsreg = $dbPortal->query($qry_schl_acad_yrprd);
		$open_schl_acad_yrprd = $rsreg->fetch_ALL();

		$counter = count($open_schl_acad_yrprd);

		$stud_container = [];

		$fetch = null;
		
		for ($x=0; $x < $counter; $x++) { 

			$cont_open_schl_acad_yrprd = $open_schl_acad_yrprd[$x];

			foreach ($cont_open_schl_acad_yrprd as $value) {

				$YR_ID  = $cont_open_schl_acad_yrprd[1];
				$PRD_ID = $cont_open_schl_acad_yrprd[2];

			$qry = "SELECT 	DISTINCT
							`schl_enr_reg`.`SchlEnrollReg_ID` `REG_ID`,
							`schl_enr_reg`.`SchlEnrollReg_DATETIME` `REG_DATE`,
							UPPER(CONCAT(	`oc_enr_paymt`.`last_name`, ', ', 
											`oc_enr_paymt`.`first_name` , ' ', 
											`oc_enr_paymt`.`middle_name`)) `NAME`,
									
							`schl_enr_reg`.`SchlEnrollReg_TYPE` `REG_TYPE`,
							`schl_acad_lvl`.`SchlAcadLvl_NAME` `LVL_NAME`, 
							`schl_acad_yrlvl`.`SchlAcadYrLvl_NAME` `YRLVL_NAME`,
							`schl_enr_adm`.`SchlAcadPrd_ID` `PRD_ID`,
							`schl_acad_crses`.`SchlAcadCrses_NAME` `CRSE_NAME`,
							
							`schl_enr_reg`.`SchlEnrollReg_STATUS` `REG_STATUS`,
							`schl_enr_adm`.`SchlEnrollAdm_STATUS` `ADM_STATUS`,
							`schl_enr_asmt`.`SchlEnrollAss_STATUS` `ASMT_STATUS`,
						
							(
								SELECT 	DISTINCT COUNT(*) `COUNT` 
								FROM 	`oc_enrollment_payments` `oc_enr_pay` 
								WHERE 	`schl_enr_reg`.`SchlEnrollReg_ID` = `oc_enr_pay`.`registration_id` AND 
										`oc_enr_pay`.`payment_isCancel` = 0) `NO_PAYMENTS`

					FROM 	`oc_enrollment_payments` `oc_enr_paymt`

					LEFT JOIN `schoolenrollmentregistration` `schl_enr_reg`
						ON `oc_enr_paymt`.`registration_id` = `schl_enr_reg`.`SchlEnrollReg_ID`	
					
					LEFT JOIN `schoolenrollmentadmission` `schl_enr_adm`				
						ON (CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

							WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
							WHEN 1 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)

							ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

						END) = `schl_enr_adm`.`SchlEnrollReg_ID`
						
					LEFT JOIN `schoolenrollmentassessment` `schl_enr_asmt`
						ON `schl_enr_adm`.`SchlEnrollAdmSms_ID` = `schl_enr_asmt`.`SchlEnrollAdm_ID`
						
					LEFT JOIN  `schoolacademiclevel` `schl_acad_lvl`
						ON `schl_enr_reg`.`SchlAcadLvl_ID` = `schl_acad_lvl`.`SchlAcadLvlSms_ID`
						
					LEFT JOIN `schoolacademicyearlevel` `schl_acad_yrlvl`
						ON `schl_enr_adm`.`SchlAcadYrLvl_ID` = `schl_acad_yrlvl`.`SchlAcadYrLvlSms_ID`
						
					LEFT JOIN `schoolacademiccourses` `schl_acad_crses`
						ON `schl_enr_adm`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID`

					WHERE 							
							`schl_enr_reg`.`SchlAcadLvl_ID`  = $lvlid  AND 
							`schl_enr_adm`.`SchlAcadYr_ID`	 = $YR_ID AND 
							`schl_enr_adm`.`SchlAcadPrd_ID`  = $PRD_ID  AND 
									
							`schl_enr_reg`.`SchlEnrollReg_ID` != 0 AND 
							`schl_enr_reg`.`SchlEnrollReg_ID` != 1 AND 
							
							`schl_enr_adm`.`SchlEnrollAdm_STATUS` IS NOT NULL  AND 

							`oc_enr_paymt`.`$stud_name_type` LIKE '%$stud_name%' and 
							
							`schl_enr_adm`.`SchlEnrollAdm_STATUS` = 1
						
					ORDER BY `schl_enr_reg`.`SchlEnrollReg_DATETIME` DESC

					";

					$rsreg = $dbPortal->query($qry);
					$stud_container = $rsreg->fetch_ALL(MYSQLI_ASSOC);

			}		
		}
		
		$fetch = $stud_container;
		$dbPortal->close();
	}

	if ($_GET['type'] == 'GET_STUDENTS_WITH_PAYMENTS') { // ***** GET ALL ASSESSED STUDENTS PAYMENTS PAYMENTS 
	
		$stud_name 		= $_GET['stud_name'];
		$stud_name_type = strtolower($_GET['stud_name_type']);
		$lvlid 			= $_GET['lvlid'];
		
		$qry_schl_acad_yrprd = "SELECT 	`SchlAcadLvl_ID` `ACAD_LVL_ID`,
										`SchlAcadYr_ID` `ACAD_YR_ID`,
										`SchlAcadPrd_ID` `ACAD_PRD_ID`

								FROM	`schoolacademicyearperiod` 

								WHERE 	`SchlAcadYrPrd_ISOPEN` = 1 AND 
										`SchlAcadLvl_ID` = $lvlid ";

		$rsreg = $dbPortal->query($qry_schl_acad_yrprd);
		$open_schl_acad_yrprd = $rsreg->fetch_ALL();

		$counter = count($open_schl_acad_yrprd);

		$stud_container = [];

		$fetch = null;
		
		for ($x=0; $x < $counter; $x++) { 

			$cont_open_schl_acad_yrprd = $open_schl_acad_yrprd[$x];

			foreach ($cont_open_schl_acad_yrprd as $value) {

				$YR_ID  = $cont_open_schl_acad_yrprd[1];
				$PRD_ID = $cont_open_schl_acad_yrprd[2];

			$qry = "SELECT 	DISTINCT
							`schl_enr_reg`.`SchlEnrollReg_ID` `REG_ID`,
							`schl_enr_reg`.`SchlEnrollReg_DATETIME` `REG_DATE`,
							UPPER(CONCAT(	`oc_enr_paymt`.`last_name`, ', ', 
											`oc_enr_paymt`.`first_name` , ' ', 
											`oc_enr_paymt`.`middle_name`)) `NAME`,
									
							`schl_enr_reg`.`SchlEnrollReg_TYPE` `REG_TYPE`,
							`schl_acad_lvl`.`SchlAcadLvl_NAME` `LVL_NAME`, 
							`schl_acad_yrlvl`.`SchlAcadYrLvl_NAME` `YRLVL_NAME`,
							`schl_enr_adm`.`SchlAcadPrd_ID` `PRD_ID`,
							`schl_acad_crses`.`SchlAcadCrses_NAME` `CRSE_NAME`,
							
							`schl_enr_reg`.`SchlEnrollReg_STATUS` `REG_STATUS`,
							`schl_enr_adm`.`SchlEnrollAdm_STATUS` `ADM_STATUS`,
						
							(
								SELECT 	DISTINCT COUNT(*) `COUNT` 
								FROM 	`oc_enrollment_payments` `oc_enr_pay` 
								WHERE 	`schl_enr_reg`.`SchlEnrollReg_ID` = `oc_enr_pay`.`registration_id` AND 
										`oc_enr_pay`.`payment_isCancel` = 0) `NO_PAYMENTS`

					FROM 	`oc_enrollment_payments` `oc_enr_paymt`

					LEFT JOIN `schoolenrollmentadmission` `schl_enr_adm` 
						ON (CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

							WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
							WHEN 1 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)

							ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

						END) = `schl_enr_adm`.`SchlEnrollReg_ID`
									
					LEFT JOIN  `schoolacademiclevel` `schl_acad_lvl`
						ON `schl_enr_reg`.`SchlAcadLvl_ID` = `schl_acad_lvl`.`SchlAcadLvlSms_ID`
						
					LEFT JOIN `schoolacademicyearlevel` `schl_acad_yrlvl`
						ON `schl_enr_adm`.`SchlAcadYrLvl_ID` = `schl_acad_yrlvl`.`SchlAcadYrLvlSms_ID`
						
					LEFT JOIN `schoolacademiccourses` `schl_acad_crses`
						ON `schl_enr_adm`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID`
					
					LEFT JOIN `oc_enrollment_payments` `oc_enr_paymt`
						ON `schl_enr_reg`.`SchlEnrollReg_ID` = `oc_enr_paymt`.`registration_id`

					WHERE 							
						(
							SELECT 	DISTINCT COUNT(*) `COUNT` 
							FROM 	`oc_enrollment_payments` `oc_enr_pay` 
							WHERE 	`schl_enr_reg`.`SchlEnrollReg_ID` = `oc_enr_pay`.`registration_id` AND 
									`oc_enr_pay`.`payment_isCancel` = 0
						) != 0 AND 
						
						`schl_enr_reg`.`SchlEnrollReg_STATUS` = 1 AND 
						`schl_enr_adm`.`SchlEnrollAdm_STATUS` = 1 AND 
						
						`oc_enr_paymt`.`$stud_name_type` LIKE '%$stud_name%' AND 

						`schl_enr_reg`.`SchlAcadLvl_ID`  = $lvlid  AND 
						`schl_enr_adm`.`SchlAcadYr_ID`	 = $YR_ID AND 
						`schl_enr_adm`.`SchlAcadPrd_ID`  = $PRD_ID  AND 
							
					ORDER BY `schl_enr_reg`.`SchlEnrollReg_DATETIME` DESC

					";

					$rsreg = $dbPortal->query($qry);
					$stud_container = $rsreg->fetch_ALL(MYSQLI_ASSOC);

			}		
		}
		
		$fetch = $stud_container;
		$dbPortal->close();
	}	

	if ($_GET['type'] == 'GET_VERIFIED_PAYMENT') { // ***** GET NUMBER OF VERIFIED PAYMENTS PER STUDENT 

		$reg_id = $_GET['registration_id'];
		
		$qry = "	SELECT DISTINCT

						IFNULL( (	SELECT COUNT(*) `COUNT`
								FROM `oc_enrollment_payments` 
								WHERE `registration_id` =  . $reg_id .  AND `payment_status` = 0)	, 0) `NOT_VERIFIED`,
							
						IFNULL(	( 	SELECT COUNT(*) `COUNT`
								FROM `oc_enrollment_payments` 
								WHERE `registration_id` =  . $reg_id . ), 0) `NUMBER_OF_PAYMENTS`	

					FROM 	`oc_enrollment_payments`";

		$rsreg = $dbPortal->query($qry);
		$fetch = $rsreg->fetch_assoc();
		$dbPortal->close();
	}

	if ($_GET['type'] == 'GET_PAYMENT_TREND') { // ***** GET PAYMNET TREND OF A STUDENT 

		$registration_id = $_GET['registration_id'];

		$qry = "SELECT 
						`oc_enr_pay`.`registration_id` `REG_ID`,
						`oc_enr_pay`.`first_name` `FNAME`,
						`oc_enr_pay`.`last_name` `LNAME`,
						`oc_enr_pay`.`middle_name` `MNANE`,
						DATE_FORMAT(`oc_enr_pay`.`payment_submitted_date`, '%M %d, %Y - %H:%i' ) AS `PAY_DATE`,
						`oc_enr_pay`.`id` `PAY_ID`,
						`oc_enr_pay`.`transaction_type` `TR_TYPE`,
						`oc_enr_pay`.`bank` `BANK`,
						`oc_enr_pay`.`amount` `AMNT`,
						`oc_enr_pay`.`reference_number` `REF_NO`,
						`oc_enr_pay`.`transaction_date` `TR_DATE`,
						`oc_enr_pay`.`payment_status` `PAY_STATUS`,
						`oc_enr_pay`.`payment_remarks` `PAY_REMARKS`,
						`oc_enr_pay`.`payment_isCancel` `PAY_ISCANCEL`
			
				FROM 	`oc_enrollment_payments` `oc_enr_pay`
				
				WHERE 	`oc_enr_pay`.`registration_id` = $registration_id
				
				ORDER BY `oc_enr_pay`.`payment_submitted_date`";

		$rsreg = $dbPortal->query($qry);
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$dbPortal->close();
	} 

	if ($_GET['type'] == 'GET_PAYMENT_ISCANCEL'){ // ***** GET CANCELLED PAYMENTS OF STUDENTS 

		$pay_id = $_GET['pay_id'];

		$qry = "SELECT 	`payment_isCancel` `isCancel` 

				FROM 	`oc_enrollment_payments`
				
				WHERE `id` = $pay_id";

		$rsreg = $dbPortal->query($qry);	
		$fetch = $rsreg->fetch_assoc();
		$dbPortal->close();	
	}

	if ($_GET['type'] == 'GET_PAYMENT_DETAILS') { // ***** GET PAYMENT DETAILS 

		$payment_id = $_GET['payment_id'];
		$lvlid 		= $_GET['lvlid'];

		// ***** GET OPEN ACADEMIC YEAR AND ACADEMIC PERIOD BASED ON ACADEMIC LEVEL 

		$qry_schl_acad_yrprd = "SELECT 	`SchlAcadLvl_ID` `ACAD_LVL_ID`,
										`SchlAcadYr_ID` `ACAD_YR_ID`,
										`SchlAcadPrd_ID` `ACAD_PRD_ID`

								FROM	`schoolacademicyearperiod` 

								WHERE 	`SchlAcadYrPrd_ISOPEN` = 1 AND 
										`SchlAcadLvl_ID` = $lvlid ";

		$rsreg = $dbPortal->query($qry_schl_acad_yrprd);
		$open_schl_acad_yrprd = $rsreg->fetch_assoc(); 

		$yrid = (int)$open_schl_acad_yrprd['ACAD_YR_ID'];
		$prdid = (int)$open_schl_acad_yrprd['ACAD_PRD_ID'];

		// ***** GET THE PAYMENT DETAILS 

		$qry = "SELECT 	`oc_enr_pay`.`registration_id` `REG_ID`,
						`oc_enr_pay`.`first_name` `FNAME`,
						`oc_enr_pay`.`last_name` `LNAME`,
						`oc_enr_pay`.`middle_name` `MNANE`,

						`schl_enr_stud_info`.`SchlEnrollRegStudInfo_EMAIL_ADD` `STUD_EMAIL`,
						`schl_enr_stud_info`.`SchlEnrollRegStudInfo_MOB_NO` `STUD_MOBNO`,
						`schl_enr_stud_info`.`SchlEnrollRegStudInfo_TEL_NO` `STUD_TELNO`,

						DATE_FORMAT(`oc_enr_pay`.`payment_submitted_date`, '%M %d, %Y - %H:%i' ) AS `PAY_DATE`,
						`oc_enr_pay`.`id` `PAY_ID`,
						`oc_enr_pay`.`transaction_type` `TR_TYPE`,
						`oc_enr_pay`.`bank` `BANK`,
						`oc_enr_pay`.`amount` `AMNT`,
						`oc_enr_pay`.`reference_number` `REF_NO`,
						`oc_enr_pay`.`transaction_date` `TR_DATE`,
						`oc_enr_pay`.`payment_status` `PAY_STATUS`,
						`oc_enr_pay`.`payment_remarks` `PAY_REMARKS`,

						`oc_upld_doc`.`document_id` `DOC_ID`,
						`oc_upld_doc`.`document_location` `DOC_LOC`

				FROM 	`oc_enrollment_payments` `oc_enr_pay`

					LEFT JOIN `schoolenrollmentregistration` `schl_enr_reg`
						ON (CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

							WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
							ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

						END) = `oc_enr_pay`.`registration_id`
						
					LEFT JOIN `schoolenrollmentadmission` `schl_enr_adm`								
						ON (CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

							WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
							ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

						END) = `schl_enr_adm`.`SchlEnrollReg_ID`
						
					LEFT JOIN `schoolenrollmentregistrationstudentinformation` `schl_enr_stud_info`
						ON (CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

							WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
							ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

						END) = `schl_enr_stud_info`.`SchlEnrollReg_ID`
						
					LEFT JOIN `oc_uploaded_documents` `oc_upld_doc`
						ON `oc_enr_pay`.`receipt_id` = `oc_upld_doc`.`document_id`

				WHERE 	
					`schl_enr_reg`.`SchlAcadLvl_ID` = $lvlid AND 
					`schl_enr_adm`.`SchlAcadYr_ID`	= $yrid  AND 
					`schl_enr_adm`.`SchlAcadPrd_ID` = $prdid AND 

					(CASE 
						WHEN `schl_enr_adm`.`SchlEnrollAdmSms_ID` = 0 THEN `schl_enr_reg`.`SchlEnrollReg_ID`
						ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)
					END 

					) = (CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

						WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
						ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)
					END) 
						AND 
					(CASE 
						WHEN `schl_enr_stud_info`.`SchlEnrollRegStudInfoSms_ID` = 0 THEN `schl_enr_reg`.`SchlEnrollReg_ID`
						ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)
					END 

					) = (CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

						WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
						ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)
					END) 
						AND 

					`oc_enr_pay`.`id` = $payment_id";

		$rsreg = $dbPortal->query($qry);
		$fetch = $rsreg->fetch_assoc();
		$dbPortal->close();
        
	}

	echo json_encode($fetch);	
?>