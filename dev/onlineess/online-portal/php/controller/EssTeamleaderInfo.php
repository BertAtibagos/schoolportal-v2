<?php

	session_start();

	include '../configuration/connection-config.php';

	// Set character set to avoid charset issues
	$dbPortal->set_charset('utf8mb4');

	if ($_GET['type'] == 'GET_ESS_NAME'){

		$fetch = array( 
			'firstname' => 'firstname',
			'lastname' => 'lastname'
		); 

		$fetch['firstname'] = $_SESSION["firstname"];
		$fetch['lastname']  = $_SESSION["lastname"];

	}
	
	if ($_GET['type'] == 'GET_ESS') {

		$qry = "SELECT 	`schlusr_id` `ID`,
						UPPER(CONCAT(`schlusr_lname`, ', ',`schlusr_fname` )) `NAME`
					
				FROM 	`oc_school_users`
				WHERE 	`schlusr_status` = 1 AND 
						`schlusr_isactive` = 1";

		$rsreg = $dbPortal->query($qry);
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$dbPortal->close();
	}

	if ($_GET['type'] == 'GET_NO_OF_FOR_ASSESSMENT_STUDENTS') {
		
		$qry_schl_acad_yrprd = "SELECT 	`SchlAcadLvl_ID` `ACAD_LVL_ID`,
										`SchlAcadYr_ID` `ACAD_YR_ID`,
										`SchlAcadPrd_ID` `ACAD_PRD_ID`

								FROM	`schoolacademicyearperiod` 

								WHERE 	`SchlAcadYrPrd_ISOPEN` = 1
								
								ORDER BY 
										`SchlAcadLvl_ID` ";

		$rsreg = $dbPortal->query($qry_schl_acad_yrprd);
		$open_schl_acad_yrprd = $rsreg->fetch_all(MYSQLI_ASSOC);

		$counter = count($open_schl_acad_yrprd);

		$fetch = 0;

		for ($x=0; $x < $counter; $x++) { 

			$cont_open_schl_acad_yrprd = $open_schl_acad_yrprd[$x];

			$LVL_ID = $cont_open_schl_acad_yrprd['ACAD_LVL_ID'];
			$YR_ID  = $cont_open_schl_acad_yrprd['ACAD_YR_ID'];
			$PRD_ID = $cont_open_schl_acad_yrprd['ACAD_PRD_ID'];


			$qry = "SELECT 	COUNT(*) `COUNT` 

					FROM	`schoolenrollmentregistration` `schl_enr_reg`

						LEFT JOIN `schoolenrollmentadmission` `schl_enr_adm`
											
							ON (CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

								WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
								ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

							END) = `schl_enr_adm`.`SchlEnrollReg_ID`

					WHERE 	
						`schl_enr_reg`.`SchlAcadLvl_ID` = $LVL_ID  AND 
						`schl_enr_reg`.`SchlAcadYr_ID`  = $YR_ID   AND 
						`schl_enr_reg`.`SchlAcadPrd_ID` = $PRD_ID  AND 
						
						`schl_enr_reg`.`SchlEnrollReg_STATUS` = 1 AND 
						`schl_enr_adm`.`SchlEnrollAdm_STATUS` = 0 ";

			$rsreg = $dbPortal->query($qry);
			$count = $rsreg->fetch_assoc();

			$fetch = $fetch + $count['COUNT'];
		}

		$dbPortal->close();



	}

	if ($_GET['type'] == 'GET_REGISTRATION_REQUIREMENTS') {

		$qry = "SELECT 	`req_name`,
						`req_code`
					
				FROM 	`oc_enrollment_requirement`";

		$rsreg = $dbPortal->query($qry);
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$dbPortal->close();
	}

	if ($_GET['type'] == 'GET_REGISTERED_STUDENTS') {

		$stud_name 		= $_GET['stud_name'];
		$stud_name_type = $_GET['stud_name_type'];
		$lvlid 			= $_GET['lvlid'];
		$adm_statusid 	= $_GET['adm_statusid'];

		$qry_schl_acad_yrprd = "SELECT 	`SchlAcadLvl_ID` `ACAD_LVL_ID`,
										`SchlAcadYr_ID` `ACAD_YR_ID`,
										`SchlAcadPrd_ID` `ACAD_PRD_ID`

								FROM	`schoolacademicyearperiod` 

								WHERE 	`SchlAcadYrPrd_ISOPEN` = 1 AND 
										`SchlAcadLvl_ID` = $lvlid ";

		$rsreg = $dbPortal->query($qry_schl_acad_yrprd);
		$open_schl_acad_yrprd = $rsreg->fetch_ALL(MYSQLI_ASSOC);

		$stud_container = [];

		foreach ($open_schl_acad_yrprd as $item) {

			$YR_ID  = $item['ACAD_YR_ID'];
			$PRD_ID = $item['ACAD_PRD_ID'];

			if($adm_statusid == "0") {
			
				$qry_contat = "	LEFT JOIN `schoolenrollmentadmission` `schl_enr_adm`								
									ON (CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

										WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
										ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

									END) = `schl_enr_adm`.`SchlEnrollReg_ID`
	
								LEFT JOIN `schoolenrollmentregistrationstudentinformation` `schl_enr_stud_info`
									ON (
										(
											IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0) = 0
											AND `schl_enr_reg`.`SchlEnrollReg_ID` = `schl_enr_stud_info`.`SchlEnrollReg_ID`
											AND IFNULL(`schl_enr_stud_info`.`SchlEnrollRegStudInfoSms_ID`, 0) = 0
										)
										OR (
											IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0) != 0
											AND `schl_enr_reg`.`SchlEnrollRegSms_ID` = `schl_enr_stud_info`.`SchlEnrollReg_ID`
										)
									)
									
								LEFT JOIN `schoolacademiccourses` `schl_acad_crses`
									ON `schl_enr_adm`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID`
								
							WHERE 							
								`schl_enr_reg`.`SchlAcadLvl_ID`  = $lvlid  AND 
								`schl_enr_reg`.`SchlAcadYr_ID`	 = $YR_ID  AND 
								`schl_enr_reg`.`SchlAcadPrd_ID`  = $PRD_ID AND 

								`schl_enr_adm`.`SchlAcadYr_ID`	 = $YR_ID AND 
								`schl_enr_adm`.`SchlAcadPrd_ID`  = $PRD_ID AND 

								`schl_enr_stud_info`.`SchlEnrollRegStudInfo_$stud_name_type` LIKE '%$stud_name%' and 
								`schl_enr_reg`.`SchlEnrollReg_STATUS` = 1 AND
								`schl_enr_adm`.`SchlEnrollAdm_STATUS` = $adm_statusid AND 
								`schl_enr_reg`. `SchlEnrollReg_ISCANCEL` = 0 -- AND
								-- `schl_enr_reg`.`SchlEnrollReg_SOURCE` = 'ONLINE'

							ORDER BY `schl_enr_reg`.`SchlEnrollReg_DATETIME` DESC	
	
							LIMIT 300";
	
			} else {
	
				$qry_contat = "	LEFT JOIN `schoolenrollmentadmission` `schl_enr_adm`								
									ON (CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

										WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
										ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

									END) = `schl_enr_adm`.`SchlEnrollReg_ID`

								LEFT JOIN `schoolenrollmentregistrationstudentinformation` `schl_enr_stud_info`
									ON (
										(
											IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0) = 0
											AND `schl_enr_reg`.`SchlEnrollReg_ID` = `schl_enr_stud_info`.`SchlEnrollReg_ID`
											AND IFNULL(`schl_enr_stud_info`.`SchlEnrollRegStudInfoSms_ID`, 0) = 0
										)
										OR (
											IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0) != 0
											AND `schl_enr_reg`.`SchlEnrollRegSms_ID` = `schl_enr_stud_info`.`SchlEnrollReg_ID`
										)
									)
									
								LEFT JOIN `schoolacademiccourses` `schl_acad_crses`
									ON `schl_enr_adm`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID`
									
								 		
								 	LEFT JOIN `schoolenrollmentassessment` `schl_enr_asmt`
								 		ON (CASE IFNULL(`schl_enr_adm`.`SchlEnrollAdmSms_ID`, 0)
								 
								 			WHEN 0 THEN IFNULL(`schl_enr_adm`.`SchlEnrollAdm_ID`, 0)
								 			ELSE IFNULL(`schl_enr_adm`.`SchlEnrollAdmSms_ID`, 0)
								 
								 		END) = `schl_enr_asmt`.`SchlEnrollAdm_ID`
								
							WHERE 							
								`schl_enr_reg`.`SchlAcadLvl_ID`  = $lvlid  AND 
								`schl_enr_reg`.`SchlAcadYr_ID`	 = $YR_ID  AND 
								`schl_enr_reg`.`SchlAcadPrd_ID`  = $PRD_ID AND 

								`schl_enr_adm`.`SchlAcadYr_ID`	 = $YR_ID AND 
								`schl_enr_adm`.`SchlAcadPrd_ID`  = $PRD_ID AND 

								`schl_enr_stud_info`.`SchlEnrollRegStudInfo_$stud_name_type` LIKE '%$stud_name%' and 
								`schl_enr_reg`.`SchlEnrollReg_STATUS` = 1 AND
								`schl_enr_adm`.`SchlEnrollAdm_STATUS` = $adm_statusid AND 
								-- `schl_enr_asmt`.`SchlEnrollAss_STATUS` = 0 AND 
								`schl_enr_reg`. `SchlEnrollReg_ISCANCEL` = 0 -- AND
								-- `schl_enr_reg`.`SchlEnrollReg_SOURCE` = 'ONLINE'
	
							ORDER BY `schl_enr_reg`.`SchlEnrollReg_DATETIME` DESC	
	
							LIMIT 300";
	
			}

			$qry = "SELECT 	(CASE IFNULL(schl_enr_reg.`SchlEnrollRegSms_ID`, 0)

							WHEN 0 THEN IFNULL(schl_enr_reg.`SchlEnrollReg_ID`, 0)
							WHEN 1 THEN IFNULL(schl_enr_reg.`SchlEnrollReg_ID`, 0)

							ELSE IFNULL(schl_enr_reg.`SchlEnrollRegSms_ID`, 0)

						END) `REG_ID`,

						(CASE IFNULL(`schl_enr_adm`.`SchlEnrollAdmSms_ID`,0)
							WHEN 0 THEN IFNULL(`schl_enr_adm`.`SchlEnrollAdm_ID`,0)
							WHEN 1 THEN  IFNULL(`schl_enr_adm`.`SchlEnrollAdm_ID`,0)
							
							ELSE IFNULL(`schl_enr_adm`.`SchlEnrollAdmSms_ID`,0)
						
						END) `ADM_ID`,  

						`schl_enr_reg`.`SchlEnrollReg_DATETIME` `REG_DATE`,

						UPPER(CONCAT(	`schl_enr_stud_info`.`SchlEnrollRegStudInfo_LAST_NAME`, ', ', 
										`schl_enr_stud_info`.`SchlEnrollRegStudInfo_FIRST_NAME` , ' ', 
										`schl_enr_stud_info`.`SchlEnrollRegStudInfo_MIDDLE_NAME`)) `NAME`,

						(CASE IFNULL(`schl_enr_reg`.`SchlEnrollReg_SOURCE`, '')
								WHEN '' THEN 'ONSITE'
								ELSE IFNULL(`schl_enr_reg`.`SchlEnrollReg_SOURCE`, 'ONSITE')
						END) `SOURCE`,

						`schl_enr_reg`.`SchlEnrollReg_TYPE` `REG_TYPE`,
						`schl_acad_crses`.`SchlAcadCrses_CODE` `CRSE_NAME`,
						`schl_enr_reg`.`SchlEnroll_Assigned_ESS` `ESS_ID` ,
						`schl_enr_reg`.`SchlEnrollReg_STATUS` `REG_STATUS`,
						`schl_enr_adm`.`SchlEnrollAdm_STATUS` `ADM_STATUS`

					FROM 	`schoolenrollmentregistration` `schl_enr_reg`
					
					$qry_contat

					";
			
			
			$rsreg = $dbPortal->query($qry);
			$stud_list = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			
			$stud_container = array_merge($stud_container, $stud_list);		
		}


		$fetch = $stud_container;

		// ini_set('xdebug.var_display_max_data', -1); // Set to -1 for unlimited data display
		// echo var_dump($qry);

	}

	if($_GET['type'] == 'GET_REGISTRATION_INFORMATION') {
		
		$lvlid 	= $_GET['lvlid'];
		$registration_id = $_GET['registration_id'];

		$qry_schl_acad_yrprd = "SELECT 	`SchlAcadLvl_ID` `ACAD_LVL_ID`,
										`SchlAcadYr_ID` `ACAD_YR_ID`,
										`SchlAcadPrd_ID` `ACAD_PRD_ID`

								FROM	`schoolacademicyearperiod` 

								WHERE 	`SchlAcadYrPrd_ISOPEN` = 1 AND 
										`SchlAcadLvl_ID` = $lvlid ";

		$rsreg = $dbPortal->query($qry_schl_acad_yrprd);
		$open_schl_acad_yrprd = $rsreg->fetch_ALL(MYSQLI_ASSOC);

		$yr_id  = (int)$open_schl_acad_yrprd[0]['ACAD_YR_ID'];
		$prd_id = (int)$open_schl_acad_yrprd[0]['ACAD_PRD_ID'];

		$qry = "SELECT 
					(CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)
				
						WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
						WHEN 1 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
				
						ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)
				
					END) `REG_ID`,
					`schl_enr_reg`.`SchlEnrollRegSP_ID` `ENR_REG_ID`,
					`schl_enr_adm`.`SchlEnrollAdm_ID` `ADM_ID`,
			
					-- EDUCATIONAL INFORMATION
				
					`schl_enr_adm`.`SchlEnrollAdm_STATUS` `ADM_STAT`,
									
					`schl_enr_reg`.`SchlEnrollReg_TYPE` `REG_TYPE`,
					`schl_enr_stud_info`.`OC_USER_ACCOUNT_ID` `OC_ACC_ID`,
					`schl_enr_reg`.`SchlAcadLvl_ID` `REG_LVLID`,
					`schl_acad_lvl`.`SchlAcadLvl_NAME` `REG_LVLNAME`,
				
					`schl_enr_adm`.`SchlEnrollAdm_TYPE` `ADM_TYPE`,
					`schl_enr_adm`.`SchlEnrollAdm_IS_FRESHMEN` `ADM_IF_FRSHMN`,
				
					`schl_acad_crses`.`SchlAcadCrseSms_ID` `ADM_CRSESID`,
					`schl_acad_crses`.`SchlAcadCrses_NAME` `ADM_CRSESNAME`,
				
					`schl_enr_adm`.`SchlAcadYrLvl_ID` `ADM_YRLVLID`,
					`schl_acad_yrlvl`.`SchlAcadYrLvl_NAME` `ADM_YRLVLNAME`,
				
					`schl_enr_adm`.`SchlAcadPrd_ID` `ADM_PRDID`,
					`schl_acad_prd`.`SchlAcadPrd_DESC` `ADM_PRDNAME`,
				
					`schl_enr_adm`.`SchlAcadYr_ID` `ADM_YRID`,
					`schl_acad_yr`.`SchlAcadYr_NAME` `ADM_YRNAME`,

					`schl_acad_asmt`.`SchlAcadSubj_ID` `REG_SUBJ_LIST`,
					
					-- STUDENT INFORMATION
								
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_LRNNO` `STUD_LRN`,
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_FIRST_NAME` `STUD_FNAME`,
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_MIDDLE_NAME` `STUD_MNAME`, 
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_LAST_NAME` `STUD_LNAME`,
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_SUFFIX_NAME` `STUD_SFFX`,
					
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_BIRTH_DATE` `STUD_BDATE`,
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_AGE` `STUD_AGE`,
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_BIRTH_PLACE` `STUD_BPLACE`,
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_NATIONALITY` `STUD_NATN`,
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_CIVILSTATUS` `STUD_CVLSTAT`,
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_GENDER` `STUD_GENDR`,
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_RELIGION` `STUD_RELGN`,
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_MOTHER_TONGUE` `STUD_MTHRTNGE`,
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_EMAIL_ADD` `STUD_EMAIL`,
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_MOB_NO` `STUD_MOBNO`,
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_TEL_NO` `STUD_TELNO`,
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_NO_OF_SIBLINGS` `STUD_SIBNO`,
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_REG_SITE` `STUD_REGSITE`,
					
					-- PERMANENT ADDRESS
								
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_PERM_ADD` `STUD_PERM_ADD`,
									
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_PERM_BRGY_ID` `STUD_PERM_BRGYID`,
					`perma_brgy`.`PhilAreaLocBrgy_NAME` `STUD_PERM_BRGYNAME`,
					
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_PERM_MUN_ID` `STUD_PERM_MUNID`,
					`perma_mun`.`PhilAreaLocMun_NAME` `STUD_PERM_MUNNAME`,
				
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_PERM_PROV_ID` `STUD_PERM_PROVID`,
					`perma_prov`.`PhilAreaLocProv_NAME` `STUD_PERM_PROVNAME`,
					
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_PERM_ZIPCODE` `STUD_PERM_ZIP`,
					
					-- PRESENT ADDRESS
						
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_PRES_ADD` `STUD_PRES_ADD`,
					
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_PRES_BRGY_ID` `STUD_PRES_BRGYID`,
					`pres_brgy`.`PhilAreaLocBrgy_NAME` `STUD_PRES_BRGYNAME`,
					
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_PRES_MUN_ID` `STUD_PRES_MUNID`,
					`pres_mun`.`PhilAreaLocMun_NAME` `STUD_PRES_MUNNAME`,
				
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_PRES_PROV_ID` `STUD_PRES_PROV_ID`,
					`pres_prov`.`PhilAreaLocProv_NAME` `STUD_PRES_PROVNAME`,
					
					`schl_enr_stud_info`.`SchlEnrollRegStudInfo_PRES_ZIPCODE` `STUD_PRES_ZIP`,
					
					-- FAMILY INFORMATION
					
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_FATHER_FIRST_NAME` 	`FTHR_FNAME`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_FATHER_MIDDLE_NAME` 	`FTHR_MNAME`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_FATHER_LAST_NAME` 	`FTHR_LNAME`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_FATHER_SUFFIX_NAME` 	`FTHR_SFFX`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_FATHER_CONTACT_NO`	`FTHR_CONTACT`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_FATHER_OCCUPATION`	`FTHR_OCCUPATION`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_FATHER_EMAIL_ADD`	`FTHR_EMAIL`,
					
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_MOTHER_FIRST_NAME` 	`MTHR_FNAME`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_MOTHER_MIDDLE_NAME` 	`MTHR_MNAME`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_MOTHER_LAST_NAME` 	`MTHR_LNAME`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_MOTHER_SUFFIX_NAME` 	`MTHR_SFFX`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_MOTHER_CONTACT_NO`	`MTHR_CONTACT`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_MOTHER_OCCUPATION`	`MTHR_OCCUPATION`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_MOTHER_EMAIL_ADD`	`MTHR_EMAIL`,
					
					
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_GUARDIAN_FIRST_NAME` 	`GRDN_FNAME`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_GUARDIAN_MIDDLE_NAME` `GRDN_MNAME`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_GUARDIAN_LAST_NAME` 	`GRDN_LNAME`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_GUARDIAN_SUFFIX_NAME` `GRDN_SFFX`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_GUARDIAN_CONTACT_NO`	`GRDN_CONTACT`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_GUARDIAN_OCCUPATION`	`GRDN_OCCUPATION`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_GUARDIAN_EMAIL_ADD`	`GRDN_EMAIL`,
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_GUARDIAN_RELATIONSHIP` `GRDN_RELATIONSHIP`,
					
					`schl_enr_fam_info`.`SchlEnrollRegFamInfo_PARENT_STATUS` `PARENT_STAT`,
					
					-- LAST SCHOOL INFORMATION 
					
					`schl_enr_educ_info`.`SchlEnrollRegEducInfo_DATE_OF_LAST_SCHL_ATTENDED` 	`LAST_SCHL_DATE`,
					`schl_enr_educ_info`.`SchlEnrollRegEducInfo_NAME_OF_LAST_SCHL_ATTENDED`		`LAST_SCHL_NAME`,
					`schl_enr_educ_info`.`SchlEnrollRegEducInfo_ADD_OF_LAST_SCHL_ATTENDED`		`LAST_SCHL_ADD`,
					`schl_enr_educ_info`.`SchlEnrollRegEducInfo_SECTOR_OF_LAST_SCHL_ATTENDED`	`LAST_SCHL_SCTR`,
					`schl_enr_educ_info`.`SchlEnrollRegEducInfo_EDUC_LEVEL_FROM_LAST_SCHL_ATTENDED`	`LAST_SCHL_EDUC_LVL`,
					`schl_enr_educ_info`.`SchlEnrollRegEducInfo_YR_LEVEL_FROM_LAST_SCHL_ATTENDED`	`LAST_SCHL_YR_LEVEL`,
					`schl_enr_educ_info`.`SchlEnrollRegEducInfo_CRSE_LEVEL_FROM_LAST_SCHL_ATTENDED`	`LAST_SCHL_CRSE`,

					-- SPECIAL ARRANGEMENT & CONDITIONS INFORMATION 
	
					`schl_enr_spec_arr_info`.`SchlEnrollRegSpclArrCondInfo_REASONS_FOR_CHOOSING_FCPC` `SPEC_REASON_FCPC`,
					`schl_enr_spec_arr_info`.`SchlEnrollRegSpclArrCondInfo_HOW_DID_YOU_KNOW_FCPC` 	  `SPEC_HOW_KNOW_FCPC`,

					`schl_enr_spec_arr_info`.`SchlEnrollRegSpclArrCondInfo_I_AM_ALLOWING_MY_CHILD` `COMMUTE`, 
					`schl_enr_spec_arr_info`.`SchlEnrollRegSpclArrCondInfo_I_HAVE_COMMISIONED_A_SHUTTLE` `SHUTTLE`,
					`schl_enr_spec_arr_info`.`SchlEnrollRegSpclArrCondInfo_THE_ONLY_PEOPLE_AUTHORIZED_TO33` `AUTHORIZED_PEOPLE`,
					`schl_enr_spec_arr_info`.`SchlEnrollRegSpclArrCondInfo_I_AM_HAVING_MY_CHILD_ACCOMPAN34` `SPECIAL_NURSEMAID`,
					`schl_enr_spec_arr_info`.`SchlEnrollRegSpclArrCondInfo_MY_CHILD_IS_NOT_TO_RECIEVE_AN35` `PACKAGE`,
					`schl_enr_spec_arr_info`.`SchlEnrollRegSpclArrCondInfo_I_AGREE_TO_RECEIVE_SMS_NOTIFI36` `NOTIFICATIONS`

				
				FROM `schoolenrollmentregistration` `schl_enr_reg`
				
					
					LEFT JOIN `schoolenrollmentadmission` `schl_enr_adm` -- FOR ADMISSION
						ON (CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

								WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
								ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

							END) = `schl_enr_adm`.`SchlEnrollReg_ID`

					LEFT JOIN `schoolenrollmentassessment` `schl_acad_asmt` -- FOR ASSESSMENT
						ON(
							(
								IFNULL(`schl_enr_adm`.`SchlEnrollAdmSms_ID`, 0) = 0
								AND `schl_enr_adm`.`SchlEnrollAdm_ID` = `schl_acad_asmt`.`SchlEnrollAdm_ID`
								AND IFNULL(`schl_acad_asmt`.`SchlEnrollAssSms_ID`, 0) = 0
							)
							OR(
								IFNULL(`schl_enr_adm`.`SchlEnrollAdmSms_ID`, 0) != 0
								AND `schl_enr_adm`.`SchlEnrollAdmSms_ID` = `schl_acad_asmt`.`SchlEnrollAdm_ID`
							)
						)
						
					LEFT JOIN `schoolenrollmentregistrationstudentinformation` `schl_enr_stud_info` -- FOR STUDENT INFORMATION

						ON (
							(
								IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0) = 0
								AND `schl_enr_reg`.`SchlEnrollReg_ID` = `schl_enr_stud_info`.`SchlEnrollReg_ID`
								AND IFNULL(`schl_enr_stud_info`.`SchlEnrollRegStudInfoSms_ID`, 0) = 0
							)
							OR (
								IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0) != 0
								AND `schl_enr_reg`.`SchlEnrollRegSms_ID` = `schl_enr_stud_info`.`SchlEnrollReg_ID`
							)
						)
					-- LEFT JOIN EDUCATION INFORMATION	
					LEFT JOIN `schoolenrollmentregistrationducationinformation` `schl_enr_educ_info`
						ON(
							IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0) = 0
							AND `schl_enr_reg`.`SchlEnrollReg_ID` = `schl_enr_educ_info`.`SchlEnrollReg_ID`
							AND IFNULL(`schl_enr_educ_info`.`SchlEnrollRegEducInfoSms_ID`, 0) = 0
						) OR (
							IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0) != 0
							AND `schl_enr_reg`.`SchlEnrollRegSms_ID` = `schl_enr_educ_info`.`SchlEnrollReg_ID`
						)
					
					-- LEFT JOIN FAMILY INFORMATION	
					LEFT JOIN `schoolenrollmentregistrationfamilyinformation` `schl_enr_fam_info`
						ON(
							IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0) = 0
							AND `schl_enr_reg`.`SchlEnrollReg_ID` = `schl_enr_fam_info`.`SchlEnrollReg_ID`
							AND IFNULL(`schl_enr_fam_info`.`SchlEnrollRegFamInfoSms_ID`, 0) = 0
						) OR (
							IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0) != 0
							AND `schl_enr_reg`.`SchlEnrollRegSms_ID` = `schl_enr_fam_info`.`SchlEnrollReg_ID`
						)

					-- LEFT JOIN SPECIAL ARRANGEMENT & CONDITIONS
					LEFT JOIN `schoolenrollmentregistrationspecialarrangementandcondition` `schl_enr_spec_arr_info`
						ON(
							IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0) = 0
							AND `schl_enr_reg`.`SchlEnrollReg_ID` = `schl_enr_spec_arr_info`.`SchlEnrollReg_ID`
							AND IFNULL(`schl_enr_spec_arr_info`.`SchlEnrollRegSpclArrCondInfoSms_ID`, 0) = 0
						) OR (
							IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0) != 0
							AND `schl_enr_reg`.`SchlEnrollRegSms_ID` = `schl_enr_spec_arr_info`.`SchlEnrollReg_ID`
						)
					
					LEFT JOIN  `schoolacademiclevel` `schl_acad_lvl`
						ON `schl_enr_reg`.`SchlAcadLvl_ID` = `schl_acad_lvl`.`SchlAcadLvlSms_ID`
					
					LEFT JOIN `schoolacademicyearlevel` `schl_acad_yrlvl`
						ON `schl_enr_adm`.`SchlAcadYrLvl_ID` = `schl_acad_yrlvl`.`SchlAcadYrLvlSms_ID`
						
					LEFT JOIN `schoolacademiccourses` `schl_acad_crses`
						ON `schl_enr_adm`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID`
					
					LEFT JOIN `schoolacademicperiod` `schl_acad_prd`
						ON `schl_enr_adm`.`SchlAcadPrd_ID` = `schl_acad_prd`.`SchlAcadPrdSms_ID`
						
					LEFT JOIN `schoolacademicyear` `schl_acad_yr`
						ON `schl_enr_adm`.`SchlAcadYr_ID` = `schl_acad_yr`.`SchlAcadYrSms_ID`
						
					LEFT JOIN `oc_ess_user` `oc_user`
						ON `schl_enr_reg`.`SchlEnroll_Assigned_ESS` = `oc_user`.`schlusr_id`
						
					-- permanent address
													
					LEFT JOIN `philippine_area_location_province` `perma_prov`
						ON `schl_enr_stud_info`.`SchlEnrollRegStudInfo_PERM_PROV_ID` = `perma_prov`.`PhilAreaLocProv_ID`
					
					LEFT JOIN `philippine_area_location_municipality` `perma_mun`
						ON `schl_enr_stud_info`.`SchlEnrollRegStudInfo_PERM_MUN_ID` = `perma_mun`.`PhilAreaLocMun_ID`
					
					LEFT JOIN `philippine_area_location_barangay` `perma_brgy`
						ON `schl_enr_stud_info`.`SchlEnrollRegStudInfo_PERM_BRGY_ID` = `perma_brgy`.`PhilAreaLocBrgy_ID`
						
					-- present address 

					LEFT JOIN `philippine_area_location_province` `pres_prov`
						ON `schl_enr_stud_info`.`SchlEnrollRegStudInfo_PRES_PROV_ID` = `pres_prov`.`PhilAreaLocProv_ID`
					
					LEFT JOIN `philippine_area_location_municipality` `pres_mun`
						ON `schl_enr_stud_info`.`SchlEnrollRegStudInfo_PRES_MUN_ID` = `pres_mun`.`PhilAreaLocMun_ID`
					
					LEFT JOIN `philippine_area_location_barangay` `pres_brgy`
						ON `schl_enr_stud_info`.`SchlEnrollRegStudInfo_PRES_BRGY_ID` = `pres_brgy`.`PhilAreaLocBrgy_ID`

							
				WHERE  
				
					(CASE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)

						WHEN 0 THEN IFNULL(`schl_enr_reg`.`SchlEnrollReg_ID`, 0)
						ELSE IFNULL(`schl_enr_reg`.`SchlEnrollRegSms_ID`, 0)
						
					END) = $registration_id -- AND 


					-- `schl_enr_reg`.`SchlAcadYr_ID`  = $yr_id  AND 
					-- `schl_enr_reg`.`SchlAcadPrd_ID` = $prd_id AND
					
					-- `schl_enr_adm`.`SchlAcadYr_ID`  = $yr_id  AND 
					-- `schl_enr_adm`.`SchlAcadPrd_ID` = $prd_id
					
				";

		$rsreg = $dbPortal->query($qry);
		$fetch = $rsreg->fetch_assoc();
		$dbPortal->close();

	}

	if ($_GET['type'] == 'GET_UPLOADED_REGISTRATION_REQUIREMENTS') {

		$reg_id = $_GET['registration_id'];

		$qry = "SELECT * 
				
				FROM `oc_uploaded_documents` 
				
				WHERE `document_id` 
					NOT IN (SELECT receipt_id FROM oc_enrollment_payments) AND registration_id = $reg_id";
	
		$rsreg = $dbPortal->query($qry);
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
	}

	if ($_GET['type'] == 'GET_WORKING_EXPERIENCE') {

		$reg_id = $_GET['reg_id'];
		$lvlid  = $_GET['lvlid'];
		$yrid   = $_GET['yrid'];
		$prdid  = $_GET['prdid'];


		$qry = "SELECT 	`schl_enr_wrk_exp`.`SchlEnrollRegWorkExp_ID` `WORK_ID`,
						`schl_enr_wrk_exp`.`SchlEnrollRegWorkExp_POSITION` `WORK_POSITION`,
						`schl_enr_wrk_exp`.`SchlEnrollRegWorkExp_COMPANY_NAME` `WORK_COMPANY_NAME`,
						`schl_enr_wrk_exp`.`SchlEnrollRegWorkExp_COMPANY_ADDRESS` `WORK_COMPANY_ADDRESS`,
						`schl_enr_wrk_exp`.`SchlEnrollRegWorkExp_DATE_EMPLOYED_FROM` `WORK_EMPLOYED_FROM`,
						`schl_enr_wrk_exp`.`SchlEnrollRegWorkExp_DATE_EMPLOYED_TO` `WORK_EMPLOYED_TO`

				FROM	`schoolenrollmentregistration` `schl_enr_reg`


					LEFT JOIN `schoolenrollmentregistrationworkingexperience` `schl_enr_wrk_exp`
					
						ON `schl_enr_reg`.`SchlEnrollRegSP_ID` = `schl_enr_wrk_exp`.`SchlEnrollReg_ID`
						
						
				WHERE 	
					`schl_enr_reg`.`SchlEnrollRegSP_ID` = $reg_id AND 
					
					`schl_enr_reg`.`SchlAcadLvl_ID` = $lvlid AND 
					`schl_enr_reg`.`SchlAcadYr_ID`  = $yrid AND 
					`schl_enr_reg`.`SchlAcadPrd_ID` = $prdid 

				ORDER BY `schl_enr_reg`.`SchlEnrollReg_DATETIME`";

		$rsreg = $dbPortal->query($qry);
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);

	}

	if ($_GET['type'] == 'GET_SECTION_SUBJECTS_SCHED'){

		$subject_list = $_GET['subject_list'];

		$qry = "SELECT  
						`schl_enr_subj_off`.`SchlEnrollSubjOffSms_ID` `ID`,
						`schl_acad_subj`.`SchlAcadSubj_CODE` `CODE`,
						`schl_acad_subj`.`SchlAcadSubj_NAME` `NAME`,
						`schl_acad_subj`.`SchlAcadSubj_DESC` `DESC`,
						`schl_enr_subj_off`.`SchlEnrollSubjOff_UNIT` `UNIT`,
						`schl_acad_sec`.`SchlAcadSec_NAME` `SEC`,
						`schl_enr_subj_off`.`SchlEnrollSubjOff_SCHEDULE_2` `SCHED`,

						(
							SELECT 
								REPLACE (GROUP_CONCAT(`schl_emp`.`SchlEmp_FNAME`, ' ', `schl_emp`.`SchlEmp_LNAME` ), ',', ' / ')
							
							FROM `schoolemployee` `schl_emp` 
							
							WHERE FIND_IN_SET( `schl_emp`.`SchlEmpSms_ID`, `schl_enr_subj_off`.`SchlProf_ID` ) 

						) `PROF`
						
				FROM `schoolenrollmentsubjectoffered` `schl_enr_subj_off`
					
					LEFT JOIN `schoolacademiccourses` `schl_acad_crses`
						ON `schl_enr_subj_off`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID`
											
					LEFT JOIN `schoolacademicsection` `schl_acad_sec`
						ON `schl_enr_subj_off`.`SchlAcadSec_ID` =  `schl_acad_sec`.`SchlAcadSecSms_ID`
						
					LEFT JOIN `schoolacademicsubject` `schl_acad_subj`
						ON `schl_enr_subj_off`.`SchlAcadSubj_ID` = `schl_acad_subj`.`SchlAcadSubjSms_ID`
						
				WHERE 	`schl_enr_subj_off`.`SchlEnrollSubjOff_STATUS` = 1 AND 

						`schl_enr_subj_off`.`SchlEnrollSubjOffSms_ID` IN ( $subject_list )
				";

		$rsreg = $dbPortal->query($qry);
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);	
		$dbPortal->close();

		//echo var_dump($qry);

	}

	echo json_encode($fetch);

?>