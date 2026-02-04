<?php
	session_start();
	require_once '../../../configuration/connection-config.php';
	//include '../../../configuration/connection-config.php';
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	
	if (isset($_GET['action']) && isset($_GET['type']))
	{
		if ($_GET['action'] == 'MANAGE'){
			if ($_GET['type'] == 'MANAGE_STUDENT_GRADES'){
				if ($_GET['mode'] == 'MANAGE' || $_GET['mode'] == 'PROCESS'){
					$mode = mysqli_real_escape_string($dbConn, $_GET['mode']);  
					$schlenrollasssms_id = intval($_GET['schlenrollasssmsid']);  
					$schlstud_id = intval($_GET['schlstudid']);  
					$schlacadgradscale_id = intval($_GET['schlacadgradscaleid']);  
					$schlenrollsubjoff_id = intval($_GET['schlenrollsubjoffid']);  
					$schlstudacadrec_id = intval($_GET['schlstudacadrecid']);  
				    $schlstudacadrecdet_id = intval($_GET['schlstudacadrecdetid']);  
				    $schlstudacadrecdet_result_type = mysqli_real_escape_string($dbConn, $_GET['schlstudacadrecdetresulttype']);  
				    $schlsign_id = intval($_GET['schlsignid']);  
				    $schlsign_userid = intval($_GET['schlsignuserid']);  
				    $schlstudacadrecdet_records = mysqli_real_escape_string($dbConn, $_GET['schlstudacadrecdetrecords']);//mysqli_real_escape_string($dbConn, $_GET['schlstudacadrecdetrecords']);  
					$reqstatus = intval($_GET['reqstatus']);  
					
					mysqli_query($dbConn ,"SET @mode='".$mode."'");
					mysqli_query($dbConn ,"SET @schlenrollasssms_id=".$schlenrollasssms_id);
					mysqli_query($dbConn ,"SET @schlstud_id=".$schlstud_id);
					mysqli_query($dbConn ,"SET @schlacadgradscale_id=".$schlacadgradscale_id);
					mysqli_query($dbConn ,"SET @schlenrollsubjoff_id=".$schlenrollsubjoff_id);
					mysqli_query($dbConn ,"SET @schlstudacadrec_id=".$schlstudacadrec_id);
					mysqli_query($dbConn ,"SET @schlstudacadrecdet_id=".$schlstudacadrecdet_id);
					mysqli_query($dbConn ,"SET @schlstudacadrecdet_result_type='".$schlstudacadrecdet_result_type."'");
					mysqli_query($dbConn ,"SET @schlsign_id=".$schlsign_id);
					mysqli_query($dbConn ,"SET @schlsign_userid=".$schlsign_userid);
					mysqli_query($dbConn ,"SET @schlstudacadrecdet_records='".$schlstudacadrecdet_records."'");
					mysqli_query($dbConn ,"SET @reqstatus=0");
					
					mysqli_multi_query($dbConn, "CALL spMANAGEstudentacademicrecord(@mode,@schlenrollasssms_id,@schlstud_id,@schlacadgradscale_id,
																        @schlenrollsubjoff_id,@schlstudacadrec_id,@schlstudacadrecdet_id,@schlstudacadrecdet_result_type,
																		@schlsign_id,@schlsign_userid,@schlstudacadrecdet_records,@reqstatus)") OR DIE (mysqli_error($dbConn));

					$status = 1;
				} 
				clearstatcache();
				mysqli_close($dbConn);
				echo $status;
			}
		} else {
			if ($_GET['type'] == 'ACADLEVEL'){
				$qry = "SELECT DISTINCT `subj_off`.`schlacadlvl_id` `ID`, 
							`acad_lvl`.`schlacadlvl_name` `NAME`
						FROM `schoolenrollmentsubjectoffered` `subj_off`
							LEFT JOIN `schoolacademiclevel` `acad_lvl`
								ON `subj_off`.`schlacadlvl_id` = `acad_lvl`.`schlacadlvlsms_id` 
						WHERE `subj_off`.`schlprof_id` = ". $_SESSION['USERID'] ."
							AND `subj_off`.`SchlEnrollSubjOff_STATUS` = 1
							AND `subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1";
				$rsreg = $dbConn->query($qry);
				$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			} else if ($_GET['type'] == 'ACADYEAR'){
				$qry ="SELECT DISTINCT	`subj_off`.`schlacadyr_id` `ID`, 
							`acad_yr`.`schlacadyr_name` `NAME`
						FROM `schoolenrollmentsubjectoffered` `subj_off`
							LEFT JOIN `schoolacademicyear` `acad_yr`
								ON `subj_off`.`schlacadyr_id` = `acad_yr`.`schlacadyrsms_id`
						WHERE `subj_off`.`schlprof_id` = ". $_SESSION['USERID'] ."
							AND `subj_off`.`schlacadlvl_id` = " . $_GET['levelid'] . " 
							AND `subj_off`.`SchlEnrollSubjOff_STATUS` = 1
							AND `subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1
						ORDER BY `acad_yr`.`SchlAcadYr_RANKNO` DESC";
				$rsreg = $dbConn->query($qry);
				$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			} else if ($_GET['type'] == 'ACADPERIOD'){
				$qry = "SELECT DISTINCT	`subj_off`.`schlacadprd_id` `ID`, 
							`acad_prd`.`schlacadprd_name` `NAME`
						FROM `schoolenrollmentsubjectoffered` `subj_off`
							LEFT JOIN `schoolacademicperiod` `acad_prd`
								ON `subj_off`.`schlacadprd_id` = `acad_prd`.`schlacadprdsms_id`
						WHERE `subj_off`.`schlprof_id`= ". $_SESSION['USERID'] ."
							AND `subj_off`.`schlacadlvl_id` = " .$_GET['levelid'] . " 
							AND `subj_off`.`schlacadyr_id`  = " .$_GET['yearid']. " 
							AND `subj_off`.`SchlEnrollSubjOff_STATUS` = 1
							AND `subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1
						ORDER BY `acad_prd`.`SchlAcadPrd_RANKNO` DESC";
				$rsreg = $dbConn->query($qry);
				$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			} else if ($_GET['type'] == 'DEPARTMENT'){
				$qry = "SELECT DISTINCT	`dept`.`SchlDeptSms_ID` `ID`, 
										`dept`.`SchlDept_NAME` `NAME`
						FROM `schoolenrollmentsubjectoffered` `subj_off`
							LEFT JOIN `schoolacademiccourses` `crse`
								ON `subj_off`.`schlacadcrses_id` = `crse`.`schlacadcrsesms_id`
							LEFT JOIN `schooldepartment` `dept`
								ON `crse`.`SchlDept_ID` = `dept`.`SchlDeptSms_ID`
						WHERE  	`subj_off`.`schlprof_id` = ". $_SESSION['USERID'] ."
							AND `subj_off`.`schlacadlvl_id` = " .$_GET['levelid'] . " 
							AND `subj_off`.`schlacadyr_id` = " .$_GET['yearid'] . " 
							AND `subj_off`.`SchlAcadPrd_ID` = " .$_GET['periodid'] . " 
							AND `subj_off`.`SchlEnrollSubjOff_STATUS` = 1
							AND `subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1
						ORDER BY `dept`.`SchlDept_NAME`";
								
				$rsreg = $dbConn->query($qry);
				$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			} else if ($_GET['type'] == 'ACADCOURSE'){
				$qry = "SELECT DISTINCT	`subj_off`.`schlacadcrses_id` `ID`, 
										`acad_crse`.`schlacadcrses_name` `NAME`
						FROM `schoolenrollmentsubjectoffered` `subj_off`
							LEFT JOIN `schoolacademiccourses` `acad_crse`
								ON `subj_off`.`schlacadcrses_id` = `acad_crse`.`schlacadcrsesms_id`
						WHERE  	`subj_off`.`schlprof_id` = ". $_SESSION['USERID'] ."
							AND `subj_off`.`schlacadlvl_id` = " .$_GET['levelid'] . " 
							AND `subj_off`.`schlacadyr_id` = " .$_GET['yearid'] . " 
							AND `subj_off`.`SchlAcadPrd_ID` = " .$_GET['periodid'] . " 
							AND `subj_off`.`SchlEnrollSubjOff_STATUS` = 1
							AND `subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1
						ORDER BY `acad_crse`.`schlacadcrses_name`";
								
				$rsreg = $dbConn->query($qry);
				$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			} else if ($_GET['type'] == 'OFFERED_SUBJECT'){
				$qry = "SELECT	CASE ISNULL(`subj`.`SchlAcadSubj_CODE`)
									WHEN 1 THEN 
										''
									ELSE
										`subj`.`SchlAcadSubj_CODE` 
								END `CODE`,
								CASE ISNULL(`subj`.`SchlAcadSubj_DESC`)
									WHEN 1 THEN 
										''
									ELSE
										`subj`.`SchlAcadSubj_DESC` 
								END `DESC`,
								CASE ISNULL(`subj`.`SchlAcadSubj_UNIT`)
									WHEN 1 THEN 
										''
									ELSE
										`subj`.`SchlAcadSubj_UNIT`
								END `UNIT`,
								CASE ISNULL(`crse`.`schlacadcrses_name`)
									WHEN 1 THEN 
										''
									ELSE
										`crse`.`schlacadcrses_name`
								END `COURSE`,
								CASE ISNULL(`sec`.`schlacadsec_name`)
									WHEN 1 THEN 
										''
									ELSE
										`sec`.`schlacadsec_name`
								END `SECTION`,
								CASE ISNULL(`subjoff`.`SchlEnrollSubjOff_SCHEDULE_2`)
									WHEN 1 THEN 
										''
									ELSE
										`subjoff`.`SchlEnrollSubjOff_SCHEDULE_2`
								END `SCHEDULE`,
								CASE ISNULL(`scale`.`SchlAcadGradScale_NAME`)
									WHEN 1 THEN 
										'(NO GS)'
									ELSE
										`scale`.`SchlAcadGradScale_NAME`
								END `GRADING_SCALE`,
								CASE ISNULL(`scale`.`SchlAcadGradScale_ID`)
									WHEN 1 THEN 
										0
									ELSE
										`scale`.`SchlAcadGradScale_ID`
								END `GS_ID`,
								(SELECT	COUNT(`ass`.`SchlEnrollAssSms_ID`) `COUNT`
									FROM `schoolenrollmentassessment` `ass`
										LEFT JOIN `schoolenrollmentadmission` `adm`
											ON `ass`.`SchlEnrollAdm_ID` = `adm`.`SchlEnrollAdmSms_ID`
										LEFT JOIN `schoolenrollmentregistration` `reg`
											ON `adm`.`SchlEnrollReg_ID` = `reg`.`SchlEnrollRegSms_ID`
									WHERE CONCAT(',',`ass`.`SchlAcadSubj_ID`,',') LIKE CONCAT('%,',`subjoff`.`SchlEnrollSubjOffSms_ID`,',%')
										AND `ass`.`schlacadlvl_id` = `subjoff`.`schlacadlvl_id`
										AND `ass`.`schlacadyr_id` = `subjoff`.`schlacadyr_id` 
										AND `ass`.`SchlAcadPrd_ID`  = `subjoff`.`SchlAcadPrd_ID`
										AND `ass`.`SchlAcadCrse_ID`  = `subjoff`.`SchlAcadCrses_ID`
										AND `ass`.`SchlEnrollAss_STATUS` = 1
										AND `adm`.`SchlEnrollAdm_STATUS` = 1
										AND `reg`.`SchlEnrollReg_STATUS` = 1) `NO_OF_STUDENT`,
								(SELECT	COUNT(`studacadrec`.`SchlStudAcadRec_ID`) `COUNT`
									FROM `schoolstudentacademicrecord` `studacadrec`
										LEFT JOIN `schoolstudentacademicrecorddetail` `studacadrecdet`
											ON CASE ISNULL(`studacadrec`.`SchlStudAcadRec_ID`)
													WHEN 1 THEN 
														0
													ELSE
														`studacadrec`.`SchlStudAcadRec_ID`
												END = 
												CASE ISNULL(`studacadrecdet`.`SchlStudAcadRec_ID`)
													WHEN 1 THEN 
														0
													ELSE
														`studacadrecdet`.`SchlStudAcadRec_ID`
												END
									WHERE CASE ISNULL(`studacadrec`.`SchlEnrollSubjOff_ID`)
													WHEN 1 THEN 
														0
													ELSE
														`studacadrec`.`SchlEnrollSubjOff_ID`
												END = CASE ISNULL(`subjoff`.`SchlEnrollSubjOffSms_ID`)
													WHEN 1 THEN 
														0
													ELSE
														`subjoff`.`SchlEnrollSubjOffSms_ID`
												END
									AND CASE ISNULL(`studacadrec`.`SchlAcadGradScale_ID`)
													WHEN 1 THEN 
														0
													ELSE
														`studacadrec`.`SchlAcadGradScale_ID`
												END = CASE ISNULL(`scale`.`SchlAcadGradScale_ID`)
													WHEN 1 THEN 
														0
													ELSE
														`scale`.`SchlAcadGradScale_ID`
												END
									AND CASE ISNULL(`studacadrec`.`SchlStudAcadRec_STATUS`)
													WHEN 1 THEN 
														0
													ELSE
														`studacadrec`.`SchlStudAcadRec_STATUS`
												END = 1 
									AND CASE ISNULL(`studacadrec`.`SchlStudAcadRec_ISACTIVE`)
													WHEN 1 THEN 
														0
													ELSE
														`studacadrec`.`SchlStudAcadRec_ISACTIVE`
												END = 1) `NO_OF_ENCODED_STUDENT`,
								CASE ISNULL(`subjoff`.`SchlEnrollSubjOffSms_ID`)
									WHEN 1 THEN 
										0 
									ELSE
										`subjoff`.`SchlEnrollSubjOffSms_ID`
								END `OFFERED_SUBJ_SMS_ID`,
								CASE ISNULL(`scale`.`SchlAcadGradScale_ID`)
									WHEN 1 THEN 
										0 
									ELSE
										`scale`.`SchlAcadGradScale_ID`
								END `GSCALE_ID`,
								CASE ISNULL(`rec`.`SchlStudAcadRec_REQ_STATUS`)
									WHEN 1 THEN 
										0 
									ELSE
										`rec`.`SchlStudAcadRec_REQ_STATUS`
								END `REQ_STATUS`,
								CASE ISNULL(`rec`.`SchlStudAcadRec_REQ_STATUS`)
									WHEN 1 THEN 
										'FOR ENCODING(GRADES)' 
									ELSE
										CASE `rec`.`SchlStudAcadRec_REQ_STATUS`
											WHEN 0 THEN -- FOR CANCEL JUST RETURN TO NUMBER ZERO
												'FOR SUBMISSION'
											WHEN 1 THEN 
												'FOR APPROVAL'
											WHEN 2 THEN 
												'FOR APPROVAL' -- Approved By Coordinator
											WHEN 3 THEN 
												'FOR APPROVAL' -- Approved By Dean/Principal
											WHEN 4 THEN 
												'APPROVED' -- Approved By Registrar (Final)
											WHEN 5 THEN 
												'DENIED'
											WHEN 6 THEN 
												'EDIT REQUEST'
											ELSE
												'UNKNOWN'
										END
								END `REQ_STATUS_NAME`,
								CASE ISNULL(`rec`.`SchlSign_ID`)
									WHEN 1 THEN 
										0 
									ELSE
										`rec`.`SchlSign_ID`
								END `SIGN_ID`,
								CASE ISNULL(`rec`.`SchlSign_UserID`)
									WHEN 1 THEN 
										0 
									ELSE
										`rec`.`SchlSign_UserID`
								END `SIGN_USERID`,
								CASE ISNULL(`rec`.`SchlStudAcadRec_ID`)
									WHEN 1 THEN 
										0 
									ELSE
										`rec`.`SchlStudAcadRec_ID`
								END `STUD_ACAD_REC_ID`,
								CASE ISNULL(`scale`.`SchlAcadGradScale_PASS_SCORE`)
									WHEN 1 THEN 
										0 
									ELSE
										`scale`.`SchlAcadGradScale_PASS_SCORE`
								END `GS_PASS_SCORE`
						FROM `schoolenrollmentsubjectoffered` `subjoff`
							LEFT JOIN `schoolacademiccourses` `crse`
								ON `subjoff`.`schlacadcrses_id` = `crse`.`schlacadcrsesms_id`
							LEFT JOIN `schoolacademicsection` `sec`
								ON `subjoff`.`SchlAcadSec_ID` = `sec`.`SchlAcadSecSms_ID`
							LEFT JOIN `schoolacademicsubject` `subj`
								ON `subjoff`.`SchlAcadSubj_ID` = `subj`.`SchlAcadSubjSms_ID`
							LEFT JOIN `schoolacademicgradingscalesubject` `scalesubj`
									ON `subjoff`.`SchlEnrollSubjOffSms_ID` = `scalesubj`.`SchlEnrollSubjOff_ID`
							LEFT JOIN `schoolacademicgradingscale` `scale`
									ON `scalesubj`.`SchlAcadGradScale_ID` = `scale`.`SchlAcadGradScale_ID`
										AND `scale`.`SchlAcadGradScale_ISPUBLISH` IS NOT NULL 
										AND `scale`.`SchlAcadGradScale_ISPUBLISH` = 1
							LEFT JOIN `schoolstudentacademicrecord` `rec`
									ON `scalesubj`.`SchlEnrollSubjOff_ID` = `rec`.`SchlEnrollSubjOff_ID`
										AND `scale`.`SchlAcadGradScale_ID` = `rec`.`SchlAcadGradScale_ID`
										AND `rec`.`SchlStudAcadRec_STATUS` = 1
										AND `rec`.`SchlStudAcadRec_ISACTIVE` = 1
						WHERE  `subjoff`.`schlprof_id` = ". $_SESSION['USERID'] ."
							AND `subjoff`.`schlacadlvl_id` = " .$_GET['levelid'] . "  
							AND `subjoff`.`schlacadyr_id` = " .$_GET['yearid'] . "  
							AND `subjoff`.`SchlAcadPrd_ID`  = " .$_GET['periodid'] . "
							AND `subjoff`.`SchlAcadCrses_ID`  = " .$_GET['courseid'] . "
							AND `subjoff`.`SchlEnrollSubjOff_STATUS` = 1
							AND `subjoff`.`SchlEnrollSubjOff_ISACTIVE` = 1
						ORDER BY `CODE`";
				$rsreg = $dbConn->query($qry);
				$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			} else if ($_GET['type'] == 'STUDENT_LIST'){
				$schlacadgradscale_id = intval($_GET['gsid']);  
				$schlacadlvl_id = intval($_GET['levelid']);  
				$schlacadyr_id = intval($_GET['yearid']);  
				$schlacadprd_id = intval($_GET['periodid']);  
				$schlacadsubj_id = mysqli_real_escape_string($dbConn, $_GET['subjofferedid']);  
				
				$qry = "CALL spGETclassliststudent(".$schlacadgradscale_id.",".$schlacadlvl_id.",".$schlacadyr_id.",".$schlacadprd_id.",'".$schlacadsubj_id."')";
				$rsreg = $dbConn->query($qry);
				$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			} else if ($_GET['type'] == 'GRADING_SCALE_INFO'){
				$qry = "SELECT	CASE ISNULL(`gscale`.`SchlAcadGradScale_NAME`)
									WHEN 1 THEN 
										'' 
									ELSE
										`gscale`.`SchlAcadGradScale_CODE`
								END `GS_CODE`,
								CASE ISNULL(`gscale`.`SchlAcadGradScale_NAME`)
									WHEN 1 THEN 
										'' 
									ELSE
										`gscale`.`SchlAcadGradScale_NAME`
								END `GS_NAME`,
								CASE ISNULL(`gscale`.`SchlAcadGradScale_DESC`)
									WHEN 1 THEN 
										'' 
									ELSE
										`gscale`.`SchlAcadGradScale_DESC`
								END `GS_DESC`,
								CASE ISNULL(`gscaledet`.`SchlAcadGradScaleDet_CODE`)
									WHEN 1 THEN 
										'' 
									ELSE
										`gscaledet`.`SchlAcadGradScaleDet_CODE`
								END `GSD_CODE`,
								CASE ISNULL(`gscaledet`.`SchlAcadGradScaleDet_NAME`)
									WHEN 1 THEN 
										'' 
									ELSE
										`gscaledet`.`SchlAcadGradScaleDet_NAME`
								END `GSD_NAME`,
								CASE ISNULL(`gscaledet`.`SchlAcadGradScaleDet_DESC`)
									WHEN 1 THEN 
										'' 
									ELSE
										`gscaledet`.`SchlAcadGradScaleDet_DESC`
								END `GSD_DESC`,
								CASE ISNULL(`gscaledet`.`SchlAcadGradScaleDet_PARENT_ID`)
									WHEN 1 THEN 
										0
									ELSE
										`gscaledet`.`SchlAcadGradScaleDet_PARENT_ID`
								END `GSD_PARENT_ID`,
								CASE ISNULL(`gscaledet`.`SchlAcadGradScaleDet_RANKNO`)
									WHEN 1 THEN 
										0
									ELSE
										`gscaledet`.`SchlAcadGradScaleDet_RANKNO`
								END `GSD_RANK_NO`,
								CASE ISNULL(`gscaledet`.`SchlAcadGradScaleDet_PERCENTAGE`)
									WHEN 1 THEN 
										0
									ELSE
										`gscaledet`.`SchlAcadGradScaleDet_PERCENTAGE`
								END `GSD_PERCENTAGE`,
								CASE ISNULL(`gscaledet`.`SchlAcadGradScaleDet_ID`)
									WHEN 1 THEN 
										0
									ELSE
										`gscaledet`.`SchlAcadGradScaleDet_ID`
								END `GSD_DET_ID`,
								CASE ISNULL(`gscale`.`SchlAcadGradScale_ID`)
									WHEN 1 THEN 
										0
									ELSE
										`gscale`.`SchlAcadGradScale_ID`
								END `GS_ID`
						FROM `schoolacademicgradingscalesubject` `gssubj`
							LEFT JOIN `schoolacademicgradingscale` `gscale`
								ON 	CASE ISNULL(`gssubj`.`SchlAcadGradScale_ID`)
										WHEN 1 THEN 
											0
										ELSE
											`gssubj`.`SchlAcadGradScale_ID`
									END = 
									CASE ISNULL(`gscale`.`SchlAcadGradScale_ID`)
										WHEN 1 THEN 
											0
										ELSE
											`gscale`.`SchlAcadGradScale_ID`
									END
							LEFT JOIN `schoolacademicgradingscaledetail` `gscaledet`
								ON 	CASE ISNULL(`gscale`.`SchlAcadGradScale_ID`)
										WHEN 1 THEN 
											0
										ELSE
											`gscale`.`SchlAcadGradScale_ID`
									END = 
									CASE ISNULL(`gscaledet`.`SchlAcadGradScale_ID`)
										WHEN 1 THEN 
											0
										ELSE
											`gscaledet`.`SchlAcadGradScale_ID`
									END
						WHERE CASE ISNULL(`gssubj`.`SchlEnrollSubjOff_ID`)
										WHEN 1 THEN 
											0
										ELSE
											`gssubj`.`SchlEnrollSubjOff_ID`
									END = " .$_GET['subjofferedid']." ". "
							AND CASE ISNULL(`gscale`.`SchlAcadGradScale_ID`)
										WHEN 1 THEN 
											0
										ELSE
											`gscale`.`SchlAcadGradScale_ID`
									END = " .$_GET['gradingscaleid']." ". " 
							AND CASE ISNULL(`gscale`.`SchlAcadGradScale_STATUS`)
									WHEN 1 THEN 
										0
									ELSE
										`gscale`.`SchlAcadGradScale_STATUS`
								END = 1
							AND CASE ISNULL(`gscale`.`SchlAcadGradScale_ISACTIVE`)
									WHEN 1 THEN 
										0
									ELSE
										`gscale`.`SchlAcadGradScale_ISACTIVE`
								END = 1
							AND CASE ISNULL(`gscaledet`.`SchlAcadGradScaleDet_STATUS`)
									WHEN 1 THEN 
										0
									ELSE
										`gscaledet`.`SchlAcadGradScaleDet_STATUS`
								END = 1
							AND CASE ISNULL(`gscaledet`.`SchlAcadGradScaleDet_ISACTIVE`)
									WHEN 1 THEN 
										0
									ELSE
										`gscaledet`.`SchlAcadGradScaleDet_ISACTIVE`
								END = 1
							ORDER BY CASE ISNULL(`gscaledet`.`SchlAcadGradScaleDet_RANKNO`)
										WHEN 1 THEN 
											0
										ELSE
											`gscaledet`.`SchlAcadGradScaleDet_RANKNO`
									 END";
				$rsreg = $dbConn->query($qry);
				$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			} else if ($_GET['type'] == 'CLASSLIST_GRADING_SCALE_INFO'){
			    $qry = "SELECT	`gscale`.`SchlAcadGradScale_CODE` `GS_CODE`,
								`gscale`.`SchlAcadGradScale_NAME` `GS_NAME`,
								`gscale`.`SchlAcadGradScale_DESC` `GS_DESC`,
								`gscaledet`.`SchlAcadGradScaleDet_CODE` `GSD_CODE`,
								`gscaledet`.`SchlAcadGradScaleDet_NAME` `GSD_NAME`,
								`gscaledet`.`SchlAcadGradScaleDet_DESC` `GSD_DESC`,
								`gscaledet`.`SchlAcadGradScaleDet_PARENT_ID` `GSD_PARENT_ID`,
								`gscaledet`.`SchlAcadGradScaleDet_RANKNO` `GSD_RANK_NO`,
								`gscaledet`.`SchlAcadGradScaleDet_PERCENTAGE` `GSD_PERCENTAGE`,
								`gscaledet`.`SchlAcadGradScaleDet_ID` `GSD_DET_ID`,
								`gscale`.`SchlAcadGradScale_ID` `GS_ID`
						FROM `schoolacademicgradingscale` `gscale`
							LEFT JOIN `schoolacademicgradingscaledetail` `gscaledet`
								ON `gscale`.`SchlAcadGradScale_ID` = `gscaledet`.`SchlAcadGradScale_ID`
						WHERE `gscale`.`SchlAcadGradScale_ID` = ".$_GET['gradingscaleid']." ". " 
						AND `gscale`.`SchlAcadGradScale_STATUS` = 1
						AND `gscale`.`SchlAcadGradScale_ISACTIVE` = 1
						AND `gscaledet`.`SchlAcadGradScaleDet_STATUS` = 1
						AND `gscaledet`.`SchlAcadGradScaleDet_ISACTIVE` = 1
						ORDER BY `gscaledet`.`SchlAcadGradScaleDet_RANKNO`";
				$rsreg = $dbConn->query($qry);
				$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			} else if ($_GET['type'] == 'GRADING_SCALE_PERCENTAGE'){
				$qry = "SELECT	CASE ISNULL(`gscale`.`SchlAcadGradScale_ID`)
									WHEN 1 THEN 
										0 
									ELSE
										`gscale`.`SchlAcadGradScale_ID`
								END `ID`,
								CASE ISNULL(`gscaledet`.`SchlAcadGradScaleDet_PARENT_ID`)
									WHEN 1 THEN 
										0 
									ELSE
										`gscaledet`.`SchlAcadGradScaleDet_PARENT_ID`
								END `GSD_PARENT_ID`,
								CASE ISNULL(`gscaledet`.`SchlAcadGradScaleDet_ID`)
									WHEN 1 THEN 
										0 
									ELSE
										`gscaledet`.`SchlAcadGradScaleDet_ID`
								END `GS_DET_ID`,
								CASE ISNULL(`gscaledet`.`SchlAcadGradScaleDet_PERCENTAGE`)
									WHEN 1 THEN 
										0 
									ELSE
										`gscaledet`.`SchlAcadGradScaleDet_PERCENTAGE`
								END `PERCENTAGE`
						FROM `schoolacademicgradingscale` `gscale`
							LEFT JOIN `schoolacademicgradingscaledetail` `gscaledet`
								ON CASE ISNULL(`gscale`.`SchlAcadGradScale_ID`)
											WHEN 1 THEN 
												0
											ELSE
												`gscale`.`SchlAcadGradScale_ID`
										END = 
										CASE ISNULL(`gscaledet`.`SchlAcadGradScale_ID`)
											WHEN 1 THEN 
												0 
											ELSE
												`gscaledet`.`SchlAcadGradScale_ID`
										END
						WHERE CASE ISNULL(`gscale`.`SchlAcadGradScale_ID`)
											WHEN 1 THEN 
												0 
											ELSE
												`gscale`.`SchlAcadGradScale_ID`
										END = " .$_GET['gradingscaleid'];
				$rsreg = $dbConn->query($qry);
				$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			} else if ($_GET['type'] == 'GET_STUDENT_GRADES_DETAIL'){
				$qry = "SELECT	CASE ISNULL(`studacadrecdet`.`SchlStudAcadRecDet_ID`)
									WHEN 1 THEN 
										0 
									ELSE
										`studacadrecdet`.`SchlStudAcadRecDet_ID`
								END `STUD_ACAD_REC_DET_ID`,
								CASE ISNULL(`studacadrecdet`.`SchlStudAcadRecDet_RECORDS`)
									WHEN 1 THEN 
										''
									ELSE
										`studacadrecdet`.`SchlStudAcadRecDet_RECORDS`
								END `STUD_ACAD_DET_REC`,
								CASE ISNULL(`studacadrecdet`.`SchlStudAcadRecDet_RESULT_TYPE`)
									WHEN 1 THEN 
										''
									ELSE
										`studacadrecdet`.`SchlStudAcadRecDet_RESULT_TYPE`
								END `RESULT_TYPE`
							FROM `schoolstudentacademicrecorddetail` `studacadrecdet`
								LEFT JOIN `schoolstudentacademicrecord` `studacadrec`
									ON `studacadrecdet`.`SchlStudAcadRec_ID` = `studacadrec`.`SchlStudAcadRec_ID`
						WHERE CASE ISNULL(`studacadrecdet`.`SchlStud_ID`)
											WHEN 1 THEN 
												0
											ELSE
												`studacadrecdet`.`SchlStud_ID`
										END = " .$_GET['schlstudid']." ". "
							AND CASE ISNULL(`studacadrecdet`.`SchlEnrollAssSms_ID`)
											WHEN 1 THEN 
												0
											ELSE
												`studacadrecdet`.`SchlEnrollAssSms_ID`
										END = " .$_GET['schlenrollasssmsid']." ". "
							AND CASE ISNULL(`studacadrec`.`SchlEnrollSubjOff_ID`)
											WHEN 1 THEN 
												0
											ELSE
												`studacadrec`.`SchlEnrollSubjOff_ID`
										END = " .$_GET['subjofferedid']." ". "
							AND CASE ISNULL(`studacadrecdet`.`SchlStudAcadRecDet_STATUS`)
											WHEN 1 THEN 
												0
											ELSE
												`studacadrecdet`.`SchlStudAcadRecDet_STATUS`
										END = 1 
							AND CASE ISNULL(`studacadrecdet`.`SchlStudAcadRecDet_ISACTIVE`)
											WHEN 1 THEN 
												0
											ELSE
												`studacadrecdet`.`SchlStudAcadRecDet_ISACTIVE`
										END = 1";
				$rsreg = $dbConn->query($qry);
				$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			} else if ($_GET['type'] == 'GET_STUDENT_GRADES'){
				$qry = "SELECT	CASE ISNULL(`studacadrec`.`SchlStudAcadRec_ID`)
									WHEN 1 THEN 
										0 
									ELSE
										`studacadrec`.`SchlStudAcadRec_ID`
								END `STUD_ACAD_REC_ID`,
								CASE ISNULL(`studacadrec`.`SchlStudAcadRec_REQ_STATUS`)
									WHEN 1 THEN 
										0 
									ELSE
										`studacadrec`.`SchlStudAcadRec_REQ_STATUS`
								END `REQ_STATUS`,
								CASE ISNULL(`studacadrec`.`SchlSign_ID`)
									WHEN 1 THEN 
										0 
									ELSE
										`studacadrec`.`SchlSign_ID`
								END `STUD_ACAD_REC_SIGN_ID`,
								CASE ISNULL(`studacadrec`.`SchlSign_UserID`)
									WHEN 1 THEN 
										0 
									ELSE
										`studacadrec`.`SchlSign_UserID`
								END `STUD_ACAD_REC_SIGN_USERID`
							FROM `schoolstudentacademicrecord` `studacadrec`
						WHERE CASE ISNULL(`studacadrec`.`SchlEnrollSubjOff_ID`)
											WHEN 1 THEN 
												0
											ELSE
												`studacadrec`.`SchlEnrollSubjOff_ID`
										END = " .$_GET['schlenrollsubjoffid']." ". "
							AND CASE ISNULL(`studacadrec`.`SchlAcadGradScale_ID`)
											WHEN 1 THEN 
												0
											ELSE
												`studacadrec`.`SchlAcadGradScale_ID`
										END = " .$_GET['schlacadgradscaleid']." ". "
							AND CASE ISNULL(`studacadrec`.`SchlStudAcadRec_STATUS`)
											WHEN 1 THEN 
												0
											ELSE
												`studacadrec`.`SchlStudAcadRec_STATUS`
										END = 1 
							AND CASE ISNULL(`studacadrec`.`SchlStudAcadRec_ISACTIVE`)
											WHEN 1 THEN 
												0
											ELSE
												`studacadrec`.`SchlStudAcadRec_ISACTIVE`
										END = 1";
				$rsreg = $dbConn->query($qry);
				$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			} else if ($_GET['type'] == 'GET_STUDENT_GRADES_STATUS'){
				$qry = "SELECT CASE ISNULL(`SchlStudAcadRec_REQ_STATUS`)
									WHEN 1 THEN 
										0 
									ELSE
										`SchlStudAcadRec_REQ_STATUS`
								END `REQ_STATUS`, 
								CASE ISNULL(`SchlSign_ID`)
									WHEN 1 THEN 
										0 
									ELSE
										`SchlSign_ID`
								END `SIGN_ID`,
								CASE ISNULL(`SchlSign_UserID`)
									WHEN 1 THEN 
										0 
									ELSE
										`SchlSign_UserID`
								END `SIGN_USER_ID`
							FROM `schoolstudentacademicrecord`
						WHERE `SchlStudAcadRec_STATUS` = 1
							AND `SchlStudAcadRec_ISACTIVE` = 1
							AND CASE ISNULL(`SchlStudAcadRec_ID`)
										WHEN 1 THEN 
											0 
										ELSE
											`SchlStudAcadRec_ID`
									END =".$_GET['schlstudacadrecid']." ". "
							AND CASE ISNULL(`SchlEnrollSubjOff_ID`)
										WHEN 1 THEN 
											0 
										ELSE
											`SchlEnrollSubjOff_ID`
									END =".$_GET['schlenrollsubjoffid'];
								
				$rsreg = $dbConn->query($qry);
				$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			}
			$rsreg->free_result();
			$dbConn->close();
			echo json_encode($fetch);
		}
	}
?>