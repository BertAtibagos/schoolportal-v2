<?php
	session_start();
	require '../../../configuration/connection-config.php';
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	if ($_POST['type'] == 'ACADLEVEL'){
		$qry = "SELECT DISTINCT `p`.`HIS_LVL_ID` `ID`, `p`.`HIS_LVL_NAME` `NAME`
					FROM (SELECT DISTINCT `subj_off`.`SchlAcadLvl_ID` `HIS_LVL_ID`,
						`subj_off`.`SchlAcadYr_ID` `HIS_YR_ID`,
						`subj_off`.`SchlAcadPrd_ID` `HIS_PRD_ID`,
						`his`.`SchlSign_ID` `HIS_SIGN_ID`,
						`lvl`.`SchlAcadLvl_NAME` `HIS_LVL_NAME`,
						`yr`.`SchlAcadYr_NAME` `HIS_YR_NAME`,
						`prd`.`SchlAcadPrd_NAME` `HIS_PRD_NAME`,
						`yr`.`SchlAcadYr_RANKNO` `HIS_YR_RANK_NO`
								FROM `schoolstudentacademicrecordapprovalhistory` `his`
									LEFT JOIN `schoolenrollmentsubjectoffered` `subj_off`
										ON `his`.`SchlEnrollSubjOff_ID` = `subj_off`.`SchlEnrollSubjOffSms_ID`
									LEFT JOIN `schoolacademiclevel` `lvl`
										ON `subj_off`.`SchlAcadLvl_ID` = `lvl`.`SchlAcadLvlSms_ID`
									LEFT JOIN `schoolacademicyear` `yr`
										ON `subj_off`.`SchlAcadYr_ID` = `yr`.`SchlAcadYrSms_ID`
									LEFT JOIN `schoolacademicperiod` `prd`
										ON `subj_off`.`SchlAcadPrd_ID` = `prd`.`SchlAcadPrdSms_ID`
								WHERE `his`.`SchlStudAcadRecAppHis_STATUS` = 1
									AND `his`.`SchlStudAcadRecAppHis_ISACTIVE` = 1
									AND `his`.`SchlStudAcadRecAppHis_ISAPPROVED` = 1
									AND `his`.`SchlSign_UserID`=  ". $_SESSION['EMPLOYEE']['ID'] ." 
					  UNION 
						  SELECT DISTINCT `subj_off`.`SchlAcadLvl_ID` `LVL_ID`,
						`subj_off`.`SchlAcadYr_ID` `YR_ID`,
						`subj_off`.`SchlAcadPrd_ID` `PRD_ID`,
						`studrec`.`SchlSign_ID` `SIGN_ID`,
						`lvl`.`SchlAcadLvl_NAME` `LVL_NAME`,
						`yr`.`SchlAcadYr_NAME` `YR_NAME`,
						`prd`.`SchlAcadPrd_NAME` `PRD_NAME`,
						`yr`.`SchlAcadYr_RANKNO` `YR_RANK_NO`
						  FROM `schoolstudentacademicrecord` `studrec`
						  LEFT JOIN `schoolenrollmentsubjectoffered` `subj_off`
										ON `studrec`.`SchlEnrollSubjOff_ID` = `subj_off`.`SchlEnrollSubjOffSms_ID`		
									LEFT JOIN `schoolacademiclevel` `lvl`
										ON `subj_off`.`SchlAcadLvl_ID` = `lvl`.`SchlAcadLvlSms_ID`
									LEFT JOIN `schoolacademicyear` `yr`
										ON `subj_off`.`SchlAcadYr_ID` = `yr`.`SchlAcadYrSms_ID`
									LEFT JOIN `schoolacademicperiod` `prd`
										ON `subj_off`.`SchlAcadPrd_ID` = `prd`.`SchlAcadPrdSms_ID`
								WHERE `subj_off`.`SchlEnrollSubjOff_STATUS` = 1
									AND `subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1
									AND `studrec`.`SchlStudAcadRec_STATUS` = 1
									AND `studrec`.`SchlStudAcadRec_ISACTIVE` = 1
									AND `studrec`.`SchlSign_ID`= ". $_SESSION['EMPLOYEE']['ID'] .") AS p
				GROUP BY `p`.`HIS_LVL_ID` ORDER BY `p`.`HIS_YR_RANK_NO` DESC";
					
		$rsreg = $dbConn->query($qry);
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->free_result();
		$dbConn->close();
		echo json_encode($fetch);
	} else if ($_POST['type'] == 'ACADYEAR'){
		$qry = "SELECT DISTINCT `p`.`HIS_YR_ID` `ID`, `p`.`HIS_YR_NAME` `NAME`
					FROM (SELECT DISTINCT `subj_off`.`SchlAcadLvl_ID` `HIS_LVL_ID`,
						`subj_off`.`SchlAcadYr_ID` `HIS_YR_ID`,
						`subj_off`.`SchlAcadPrd_ID` `HIS_PRD_ID`,
						`his`.`SchlSign_ID` `HIS_SIGN_ID`,
						`lvl`.`SchlAcadLvl_NAME` `HIS_LVL_NAME`,
						`yr`.`SchlAcadYr_NAME` `HIS_YR_NAME`,
						`prd`.`SchlAcadPrd_NAME` `HIS_PRD_NAME`,
						`yr`.`SchlAcadYr_RANKNO` `HIS_YR_RANK_NO`
								FROM `schoolstudentacademicrecordapprovalhistory` `his`
									LEFT JOIN `schoolenrollmentsubjectoffered` `subj_off`
										ON `his`.`SchlEnrollSubjOff_ID` = `subj_off`.`SchlEnrollSubjOffSms_ID`
									LEFT JOIN `schoolacademiclevel` `lvl`
										ON `subj_off`.`SchlAcadLvl_ID` = `lvl`.`SchlAcadLvlSms_ID`
									LEFT JOIN `schoolacademicyear` `yr`
										ON `subj_off`.`SchlAcadYr_ID` = `yr`.`SchlAcadYrSms_ID`
									LEFT JOIN `schoolacademicperiod` `prd`
										ON `subj_off`.`SchlAcadPrd_ID` = `prd`.`SchlAcadPrdSms_ID`
								WHERE `his`.`SchlStudAcadRecAppHis_STATUS` = 1
									AND `his`.`SchlStudAcadRecAppHis_ISACTIVE` = 1
									AND `his`.`SchlStudAcadRecAppHis_ISAPPROVED` = 1
									AND `his`.`SchlSign_UserID`=  ". $_SESSION['EMPLOYEE']['ID'] ."
									AND `subj_off`.`SchlAcadLvl_ID` = " .$_POST['levelid'] ."
					  UNION 
						  SELECT DISTINCT `subj_off`.`SchlAcadLvl_ID` `LVL_ID`,
						`subj_off`.`SchlAcadYr_ID` `YR_ID`,
						`subj_off`.`SchlAcadPrd_ID` `PRD_ID`,
						`studrec`.`SchlSign_ID` `SIGN_ID`,
						`lvl`.`SchlAcadLvl_NAME` `LVL_NAME`,
						`yr`.`SchlAcadYr_NAME` `YR_NAME`,
						`prd`.`SchlAcadPrd_NAME` `PRD_NAME`,
						`yr`.`SchlAcadYr_RANKNO` `YR_RANK_NO`
						  FROM `schoolstudentacademicrecord` `studrec`
						  LEFT JOIN `schoolenrollmentsubjectoffered` `subj_off`
										ON `studrec`.`SchlEnrollSubjOff_ID` = `subj_off`.`SchlEnrollSubjOffSms_ID`		
									LEFT JOIN `schoolacademiclevel` `lvl`
										ON `subj_off`.`SchlAcadLvl_ID` = `lvl`.`SchlAcadLvlSms_ID`
									LEFT JOIN `schoolacademicyear` `yr`
										ON `subj_off`.`SchlAcadYr_ID` = `yr`.`SchlAcadYrSms_ID`
									LEFT JOIN `schoolacademicperiod` `prd`
										ON `subj_off`.`SchlAcadPrd_ID` = `prd`.`SchlAcadPrdSms_ID`
								WHERE `subj_off`.`SchlEnrollSubjOff_STATUS` = 1
									AND `subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1
									AND `studrec`.`SchlStudAcadRec_STATUS` = 1
									AND `studrec`.`SchlStudAcadRec_ISACTIVE` = 1
									AND `studrec`.`SchlSign_ID`= ". $_SESSION['EMPLOYEE']['ID'] ."
									AND `subj_off`.`SchlAcadLvl_ID` = " .$_POST['levelid'] .") AS p
				GROUP BY `p`.`HIS_YR_ID` ORDER BY `p`.`HIS_YR_RANK_NO` DESC";
					
		$rsreg = $dbConn->query($qry);
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->free_result();
		$dbConn->close();
		echo json_encode($fetch);
	} else if ($_POST['type'] == 'ACADPERIOD'){
		$qry = "SELECT DISTINCT `p`.`HIS_PRD_ID` `ID`, `p`.`HIS_PRD_NAME` `NAME`
					FROM (SELECT DISTINCT `subj_off`.`SchlAcadLvl_ID` `HIS_LVL_ID`,
						`subj_off`.`SchlAcadYr_ID` `HIS_YR_ID`,
						`subj_off`.`SchlAcadPrd_ID` `HIS_PRD_ID`,
						`his`.`SchlSign_ID` `HIS_SIGN_ID`,
						`lvl`.`SchlAcadLvl_NAME` `HIS_LVL_NAME`,
						`yr`.`SchlAcadYr_NAME` `HIS_YR_NAME`,
						`prd`.`SchlAcadPrd_NAME` `HIS_PRD_NAME`,
						`prd`.`SchlAcadPrd_RANKNO` `HIS_PRD_RANK_NO`
								FROM `schoolstudentacademicrecordapprovalhistory` `his`
									LEFT JOIN `schoolenrollmentsubjectoffered` `subj_off`
										ON `his`.`SchlEnrollSubjOff_ID` = `subj_off`.`SchlEnrollSubjOffSms_ID`
									LEFT JOIN `schoolacademiclevel` `lvl`
										ON `subj_off`.`SchlAcadLvl_ID` = `lvl`.`SchlAcadLvlSms_ID`
									LEFT JOIN `schoolacademicyear` `yr`
										ON `subj_off`.`SchlAcadYr_ID` = `yr`.`SchlAcadYrSms_ID`
									LEFT JOIN `schoolacademicperiod` `prd`
										ON `subj_off`.`SchlAcadPrd_ID` = `prd`.`SchlAcadPrdSms_ID`
								WHERE `his`.`SchlStudAcadRecAppHis_STATUS` = 1
									AND `his`.`SchlStudAcadRecAppHis_ISACTIVE` = 1
									AND `his`.`SchlStudAcadRecAppHis_ISAPPROVED` = 1
									AND `his`.`SchlSign_UserID`=  ". $_SESSION['EMPLOYEE']['ID'] ."
									AND `subj_off`.`SchlAcadLvl_ID` = " .$_POST['levelid'] ."
									AND `subj_off`.`SchlAcadYr_ID` = " .$_POST['yearid'] ."
					  UNION 
						  SELECT DISTINCT `subj_off`.`SchlAcadLvl_ID` `LVL_ID`,
						`subj_off`.`SchlAcadYr_ID` `YR_ID`,
						`subj_off`.`SchlAcadPrd_ID` `PRD_ID`,
						`studrec`.`SchlSign_ID` `SIGN_ID`,
						`lvl`.`SchlAcadLvl_NAME` `LVL_NAME`,
						`yr`.`SchlAcadYr_NAME` `YR_NAME`,
						`prd`.`SchlAcadPrd_NAME` `PRD_NAME`,
						`prd`.`SchlAcadPrd_RANKNO` `PRD_RANK_NO`
						  FROM `schoolstudentacademicrecord` `studrec`
						  LEFT JOIN `schoolenrollmentsubjectoffered` `subj_off`
										ON `studrec`.`SchlEnrollSubjOff_ID` = `subj_off`.`SchlEnrollSubjOffSms_ID`		
									LEFT JOIN `schoolacademiclevel` `lvl`
										ON `subj_off`.`SchlAcadLvl_ID` = `lvl`.`SchlAcadLvlSms_ID`
									LEFT JOIN `schoolacademicyear` `yr`
										ON `subj_off`.`SchlAcadYr_ID` = `yr`.`SchlAcadYrSms_ID`
									LEFT JOIN `schoolacademicperiod` `prd`
										ON `subj_off`.`SchlAcadPrd_ID` = `prd`.`SchlAcadPrdSms_ID`
								WHERE `subj_off`.`SchlEnrollSubjOff_STATUS` = 1
									AND `subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1
									AND `studrec`.`SchlStudAcadRec_STATUS` = 1
									AND `studrec`.`SchlStudAcadRec_ISACTIVE` = 1
									AND `studrec`.`SchlSign_ID`= ". $_SESSION['EMPLOYEE']['ID'] ." 
									AND `subj_off`.`SchlAcadLvl_ID` = " .$_POST['levelid'] ." 
									AND `subj_off`.`SchlAcadYr_ID` = " .$_POST['yearid'] .") AS p
				GROUP BY `p`.`HIS_PRD_ID` ORDER BY `p`.`HIS_PRD_RANK_NO` DESC";
					
		$rsreg = $dbConn->query($qry);
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->free_result();
		$dbConn->close();
		echo json_encode($fetch);
	} else if ($_POST['type'] == 'FOR_APPROVAL_REQUEST_LIST'){
		$qry = "SELECT `studrec`.`SchlStudAcadRec_DATETIME` `DATE`,
					CONCAT(CASE ISNULL(`emp`.`SchlEmp_LNAME`)
									WHEN 1 THEN 
										''
									ELSE
										`emp`.`SchlEmp_LNAME`
								END,', ', 
								CASE ISNULL(`emp`.`SchlEmp_FNAME`)
									WHEN 1 THEN 
										''
									ELSE
										`emp`.`SchlEmp_FNAME`
								END, ' ' , 
								CASE ISNULL(`emp`.`SchlEmp_MNAME`)
									WHEN 1 THEN 
										''
									ELSE
										`emp`.`SchlEmp_MNAME`
								END) `NAME`,
					`subj`.`SchlAcadSubj_CODE` `SUBJECT`,
					CONCAT(`crse`.`SchlAcadCrses_CODE`, ' - ' ,`sec`.`SchlAcadSec_NAME`) `CRSE_SEC`,


					CASE ISNULL(`studrec`.`SchlStudAcadRec_REQ_STATUS`)
						WHEN 1 THEN 
							0 
						ELSE
							CASE `studrec`.`SchlStudAcadRec_REQ_STATUS`
								WHEN 1 THEN 
									'GRADE SUBMITTED' 
								WHEN 3 THEN 
									'SUBMIT GRADES' 
								ELSE
									'REQUEST EDIT GRADES'
							END
					END `STATUS_NAME`,
					CASE ISNULL(`studrec`.`SchlStudAcadRec_REQ_STATUS`)
						WHEN 1 THEN 
							0 
						ELSE
							`studrec`.`SchlStudAcadRec_REQ_STATUS`
					END `STATUS`,


					`gs`.`SchlAcadGradScale_NAME` `GRADING_SCALE`,
					`studrec`.`SchlStudAcadRec_ID` `STUD_REC_ID`,
					CASE ISNULL(`subj_off`.`SchlEnrollSubjOffSms_ID`)
						WHEN 1 THEN 
							0 
						ELSE
							`subj_off`.`SchlEnrollSubjOffSms_ID`
					END `OFFERED_SUBJ_SMS_ID`,
					CASE ISNULL(`gs`.`SchlAcadGradScale_ID`)
						WHEN 1 THEN 
							0 
						ELSE
							`gs`.`SchlAcadGradScale_ID`
					END `GS_ID`,
					CASE ISNULL(`subj_off`.`SchlAcadLvl_ID`)
						WHEN 1 THEN 
							0 
						ELSE
							`subj_off`.`SchlAcadLvl_ID`
					END `LVL_ID`,
					CASE ISNULL(`subj_off`.`SchlAcadYr_ID`)
						WHEN 1 THEN 
							0 
						ELSE
							`subj_off`.`SchlAcadYr_ID`
					END `YR_ID`,
					CASE ISNULL(`subj_off`.`SchlAcadPrd_ID`)
						WHEN 1 THEN 
							0 
						ELSE
							`subj_off`.`SchlAcadPrd_ID`
					END `PRD_ID`,
					CASE ISNULL(`studrec`.`SchlSign_ID`)
						WHEN 1 THEN 
							0 
						ELSE
							`studrec`.`SchlSign_ID`
					END `SIGN_ID`,
					CASE ISNULL(`gs`.`SchlAcadGradScale_PASS_SCORE`)
						WHEN 1 THEN 
							0 
						ELSE
							`gs`.`SchlAcadGradScale_PASS_SCORE`
					END `GS_PASS_SCORE`,
					CASE ISNULL(`studrec`.`SchlSign_UserID`)
						WHEN 1 THEN 
							0 
						ELSE
							`studrec`.`SchlSign_UserID`
					END `SIGN_USERID`
				FROM `schoolstudentacademicrecord` `studrec`
					LEFT JOIN `schoolenrollmentsubjectoffered` `subj_off`
						ON `studrec`.`SchlEnrollSubjOff_ID` = `subj_off`.`SchlEnrollSubjOffSms_ID`		
					LEFT JOIN `schoolacademicsubject` `subj`
						ON `subj_off`.`SchlAcadSubj_ID` = `subj`.`SchlAcadSubjSms_ID`			
					LEFT JOIN `schoolacademicgradingscale` `gs`
						ON `studrec`.`SchlAcadGradScale_ID` = `gs`.`SchlAcadGradScale_ID`
					LEFT JOIN `schoolacademiccourses` `crse`
						ON `subj_off`.`SchlAcadCrses_ID` = `crse`.`SchlAcadCrseSms_ID`
					LEFT JOIN `schoolacademiclevel` `lvl`
						ON `subj_off`.`SchlAcadLvl_ID` = `lvl`.`SchlAcadLvlSms_ID`
					LEFT JOIN `schoolacademicsection` `sec`
						ON `subj_off`.`SchlAcadSec_ID` = `sec`.`SchlAcadSecSms_ID`
					LEFT JOIN `schoolemployee` `emp`
						ON `subj_off`.`SchlProf_ID` = `emp`.`SchlEmpSms_ID`
				WHERE `subj_off`.`SchlEnrollSubjOff_STATUS` = 1
					AND `subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1
					AND `studrec`.`SchlStudAcadRec_STATUS` = 1
					AND `studrec`.`SchlStudAcadRec_ISACTIVE` = 1
					AND `studrec`.`SchlStudAcadRec_REQ_STATUS` <> 0
					AND `studrec`.`SchlSign_ID`= ". $_SESSION['EMPLOYEE']['ID']  . "
					AND `subj_off`.`SchlAcadLvl_ID` = " .$_POST['levelid'] . "
					AND `subj_off`.`SchlAcadYr_ID` = " .$_POST['yearid'] . "
					AND `subj_off`.`SchlAcadPrd_ID` = " .$_POST['periodid'] . "
				ORDER BY `NAME`";
			
		$rsreg = $dbConn->query($qry);
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->free_result();
		$dbConn->close();
		echo json_encode($fetch);
	} else if ($_POST['type'] == 'GET_STUDENT_GRADES_DETAIL'){
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
										END = " .$_POST['schlstudid']."
							AND CASE ISNULL(`studacadrecdet`.`SchlEnrollAssSms_ID`)
											WHEN 1 THEN 
												0
											ELSE
												`studacadrecdet`.`SchlEnrollAssSms_ID`
										END = " .$_POST['schlenrollasssmsid']."
							AND CASE ISNULL(`studacadrec`.`SchlEnrollSubjOff_ID`)
											WHEN 1 THEN 
												0
											ELSE
												`studacadrec`.`SchlEnrollSubjOff_ID`
										END = " .$_POST['subjofferedid']."
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
				$rsreg->free_result();
				$dbConn->close();
				echo json_encode($fetch);
	} else if ($_POST['type'] == 'GRADING_SCALE_INFO'){
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
									END = " .$_POST['subjofferedid']."
							AND CASE ISNULL(`gscale`.`SchlAcadGradScale_ID`)
										WHEN 1 THEN 
											0
										ELSE
											`gscale`.`SchlAcadGradScale_ID`
									END = " .$_POST['gradingscaleid']."
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
				$rsreg->free_result();
				$dbConn->close();
				echo json_encode($fetch);
	} else if ($_POST['type'] == 'GRADING_SCALE_PERCENTAGE'){
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
										END = " .$_POST['gradingscaleid'];
				$rsreg = $dbConn->query($qry);
				$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
				$rsreg->free_result();
				$dbConn->close();
				echo json_encode($fetch);
	} else if ($_POST['type'] == 'GRADING_SCALE_INFO'){
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
									END = " .$_POST['subjofferedid']."
							AND CASE ISNULL(`gscale`.`SchlAcadGradScale_ID`)
										WHEN 1 THEN 
											0
										ELSE
											`gscale`.`SchlAcadGradScale_ID`
									END = " .$_POST['gradingscaleid']."
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
				$rsreg->free_result();
				$dbConn->close();
				echo json_encode($fetch);
	} else if ($_POST['type'] == 'STUDENT_LIST'){
				$schlacadgradscale_id = intval($_POST['gsid']);  
				$schlacadlvl_id = intval($_POST['levelid']);  
				$schlacadyr_id = intval($_POST['yearid']);  
				$schlacadprd_id = intval($_POST['periodid']);  
				$schlacadsubj_id = mysqli_real_escape_string($dbConn, $_POST['subjofferedid']);
				$schlsubjoffered_id = intval($_POST['subjofferedid']);  
				$user_id = intval($_SESSION['EMPLOYEE']['ID'] );
				$studacadrecid =intval($_POST['studacadrecid']); 
				
			    $qry = "CALL spGETclassliststudent(".$schlacadgradscale_id.",".$schlacadlvl_id.",".$schlacadyr_id.",".$schlacadprd_id.",'".$schlacadsubj_id."',".$schlsubjoffered_id.",".$studacadrecid.",".$user_id.")";
				$rsreg = $dbConn->query($qry);
				$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
				$rsreg->free_result();
				$dbConn->close();
				echo json_encode($fetch);
	} else if ($_POST['type'] == 'MANAGE_SUBMITTED_REQUEST'){
				$mode = mysqli_real_escape_string($dbConn, $_POST['mode']);  
				$schlenrollasssms_id = intval($_POST['schlenrollasssmsid']);  
				$schlstud_id = intval($_POST['schlstudid']);  
				$schlacadgradscale_id = intval($_POST['schlacadgradscaleid']);  
				$schlenrollsubjoff_id = intval($_POST['schlenrollsubjoffid']);  
				$schlstudacadrec_id = intval($_POST['schlstudacadrecid']);  
				$schlstudacadrecdet_id = intval($_POST['schlstudacadrecdetid']);  
				$schlstudacadrecdet_result_type = mysqli_real_escape_string($dbConn, $_POST['schlstudacadrecdetresulttype']);  
				$schlsign_id = intval($_POST['schlsignid']);  
				$schlsign_userid = intval($_POST['schlsignuserid']);  
				$schlstudacadrecdet_records = mysqli_real_escape_string($dbConn, $_POST['schlstudacadrecdetrecords']);
				$reqstatus = intval($_POST['reqstatus']);  
				$userid = intval($_SESSION['EMPLOYEE']['ID'] );  
				
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
				mysqli_query($dbConn ,"SET @reqstatus=".$reqstatus);
				mysqli_query($dbConn ,"SET @userid=".$userid);
				
				mysqli_multi_query($dbConn, "CALL spMANAGEstudentacademicrecord(@mode,@schlenrollasssms_id,@schlstud_id,@schlacadgradscale_id,
																	@schlenrollsubjoff_id,@schlstudacadrec_id,@schlstudacadrecdet_id,@schlstudacadrecdet_result_type,
																	@schlsign_id,@schlsign_userid,@schlstudacadrecdet_records,@reqstatus,@userid)") OR DIE (mysqli_error($dbConn));

				$status = 1;
				mysqli_close($dbConn);
				//clearstatcache();
				echo $status;
	} else if ($_POST['type'] == 'GET_SUBMITTED_REQUEST_HISTORY'){
		$qry = "SELECT `his`.`SchlStudAcadRecAppHis_DATETIME` `DATE`,
					CONCAT(CASE ISNULL(`emp`.`SchlEmp_LNAME`)
									WHEN 1 THEN 
										''
									ELSE
										`emp`.`SchlEmp_LNAME`
								END,', ', 
								CASE ISNULL(`emp`.`SchlEmp_FNAME`)
									WHEN 1 THEN 
										''
									ELSE
										`emp`.`SchlEmp_FNAME`
								END, ' ' , 
								CASE ISNULL(`emp`.`SchlEmp_MNAME`)
									WHEN 1 THEN 
										''
									ELSE
										`emp`.`SchlEmp_MNAME`
								END) `NAME`,
					`subj`.`SchlAcadSubj_CODE` `SUBJECT`,
					CONCAT(`crse`.`SchlAcadCrses_CODE`, ' - ' ,`sec`.`SchlAcadSec_NAME`) `CRSE_SEC`,
					CASE ISNULL(`his`.`SchlStudAcadRecAppHis_REQ_STATUS`)
						WHEN 1 THEN 
							'UNKNOWN'
						ELSE
							CASE `his`.`SchlStudAcadRecAppHis_REQ_STATUS`
								WHEN 1 THEN 
									'SUBMIT GRADE' 
								WHEN 3 THEN 
									'SUBMIT GRADE' 
								WHEN 5 THEN 
									'SUBMIT GRADE' 
								WHEN 6 THEN 
									'REQUEST(EDIT GRADES)' 
								WHEN 7 THEN 
									'REQUEST(EDIT GRADES)' 
								ELSE
									'UNKNOWN'
							END
					END `STATUS_NAME`,
					CASE ISNULL(`his`.`SchlStudAcadRecAppHis_REQ_STATUS`)
						WHEN 1 THEN 
							0 
						ELSE
							`his`.`SchlStudAcadRecAppHis_REQ_STATUS`
					END `STATUS`,
					`gs`.`SchlAcadGradScale_NAME` `GRADING_SCALE`,
					`his`.`SchlStudAcadRec_ID` `STUD_REC_ID`,
					CASE ISNULL(`subj_off`.`SchlEnrollSubjOffSms_ID`)
						WHEN 1 THEN 
							0 
						ELSE
							`subj_off`.`SchlEnrollSubjOffSms_ID`
					END `OFFERED_SUBJ_SMS_ID`,
					CASE ISNULL(`gs`.`SchlAcadGradScale_ID`)
						WHEN 1 THEN 
							0 
						ELSE
							`gs`.`SchlAcadGradScale_ID`
					END `GS_ID`,
					CASE ISNULL(`subj_off`.`SchlAcadLvl_ID`)
						WHEN 1 THEN 
							0 
						ELSE
							`subj_off`.`SchlAcadLvl_ID`
					END `LVL_ID`,
					CASE ISNULL(`subj_off`.`SchlAcadYr_ID`)
						WHEN 1 THEN 
							0 
						ELSE
							`subj_off`.`SchlAcadYr_ID`
					END `YR_ID`,
					CASE ISNULL(`subj_off`.`SchlAcadPrd_ID`)
						WHEN 1 THEN 
							0 
						ELSE
							`subj_off`.`SchlAcadPrd_ID`
					END `PRD_ID`,
					CASE ISNULL(`his`.`SchlSign_ID`)
						WHEN 1 THEN 
							0 
						ELSE
							`his`.`SchlSign_ID`
					END `SIGN_ID`,
					CASE ISNULL(`gs`.`SchlAcadGradScale_PASS_SCORE`)
						WHEN 1 THEN 
							0 
						ELSE
							`gs`.`SchlAcadGradScale_PASS_SCORE`
					END `GS_PASS_SCORE`,
					CASE ISNULL(`his`.`SchlSign_UserID`)
						WHEN 1 THEN 
							0 
						ELSE
							`his`.`SchlSign_UserID`
					END `SIGN_USERID`
				FROM `schoolstudentacademicrecordapprovalhistory` `his`
					LEFT JOIN `schoolenrollmentsubjectoffered` `subj_off`
						ON `his`.`SchlEnrollSubjOff_ID` = `subj_off`.`SchlEnrollSubjOffSms_ID`		
					LEFT JOIN `schoolacademicsubject` `subj`
						ON `subj_off`.`SchlAcadSubj_ID` = `subj`.`SchlAcadSubjSms_ID`			
					LEFT JOIN `schoolacademicgradingscale` `gs`
						ON `his`.`SchlAcadGradScale_ID` = `gs`.`SchlAcadGradScale_ID`
					LEFT JOIN `schoolacademiccourses` `crse`
						ON `subj_off`.`SchlAcadCrses_ID` = `crse`.`SchlAcadCrseSms_ID`
					LEFT JOIN `schoolacademiclevel` `lvl`
						ON `subj_off`.`SchlAcadLvl_ID` = `lvl`.`SchlAcadLvlSms_ID`
					LEFT JOIN `schoolacademicsection` `sec`
						ON `subj_off`.`SchlAcadSec_ID` = `sec`.`SchlAcadSecSms_ID`
					LEFT JOIN `schoolemployee` `emp`
						ON `subj_off`.`SchlProf_ID` = `emp`.`SchlEmpSms_ID`
				WHERE `his`.`SchlStudAcadRecAppHis_STATUS` = 1
					AND `his`.`SchlStudAcadRecAppHis_ISACTIVE` = 1
					AND `his`.`SchlStudAcadRecAppHis_ISAPPROVED` = " .$_POST['isapproved'] ."
					AND `his`.`SchlSign_UserID`=  ". $_SESSION['EMPLOYEE']['ID'] ."
					AND `subj_off`.`SchlAcadLvl_ID` = " .$_POST['levelid'] ."
					AND `subj_off`.`SchlAcadYr_ID` = " .$_POST['yearid']."
					AND `subj_off`.`SchlAcadPrd_ID` = " .$_POST['periodid'];
					
		$rsreg = $dbConn->query($qry);
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->free_result();
		$dbConn->close();
		echo json_encode($fetch);
	}
?>