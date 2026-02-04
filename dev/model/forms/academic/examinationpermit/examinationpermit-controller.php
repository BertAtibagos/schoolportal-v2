<?php
	session_start();
	require_once '../../../configuration/connection-config.php';
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	
	if (isset($_POST['action']) && isset($_POST['type']))
	{
		if ($_POST['type'] == 'ACADLEVEL'){
			$qry = "SELECT `acad_lvl`.`schlacadlvlsms_id` `ID`, 
							`acad_lvl`.`schlacadlvl_name` `NAME`
						FROM `schoolenrollmentsubjectoffered` `subj_off`
							LEFT JOIN `schoolacademiclevel` `acad_lvl`
								ON `subj_off`.`schlacadlvl_id` = `acad_lvl`.`schlacadlvlsms_id` 
						WHERE CONCAT(',',`subj_off`.`schlprof_id`,',') LIKE CONCAT('%,', ". $_SESSION['EMPLOYEE']['ID'] . ",',%')
							AND `subj_off`.`SchlEnrollSubjOff_STATUS` = 1
							AND `subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1
						GROUP BY `acad_lvl`.`schlacadlvl_name`,`acad_lvl`.`schlacadlvlsms_id`
						ORDER BY `acad_lvl`.`schlacadlvl_name` DESC;";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		} else if ($_POST['type'] == 'ACADYEAR'){
			$qry ="SELECT `acad_yr`.`schlacadyrsms_id` `ID`, 
							`acad_yr`.`schlacadyr_name` `NAME`
						FROM `schoolenrollmentsubjectoffered` `subj_off`
							LEFT JOIN `schoolacademicyear` `acad_yr`
								ON `subj_off`.`schlacadyr_id` = `acad_yr`.`schlacadyrsms_id`
						WHERE CONCAT(',',`subj_off`.`schlprof_id`,',') LIKE CONCAT('%,', ". $_SESSION['EMPLOYEE']['ID'] . ",',%')
							AND `subj_off`.`schlacadlvl_id` = " . $_POST['levelid'] . "
							AND `subj_off`.`SchlEnrollSubjOff_STATUS` = 1
							AND `subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1
						GROUP BY `acad_yr`.`schlacadyr_name`,`acad_yr`.`schlacadyrsms_id`
						ORDER BY `acad_yr`.`SchlAcadYr_RANKNO` DESC;";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		} else if ($_POST['type'] == 'ACADPERIOD'){
			$qry = "SELECT `acad_prd`.`schlacadprdsms_id` `ID`, 
							`acad_prd`.`schlacadprd_name` `NAME`
						FROM `schoolenrollmentsubjectoffered` `subj_off`
							LEFT JOIN `schoolacademicperiod` `acad_prd`
								ON `subj_off`.`schlacadprd_id` = `acad_prd`.`schlacadprdsms_id`
						WHERE CONCAT(',',`subj_off`.`schlprof_id`,',') LIKE CONCAT('%,', ". $_SESSION['EMPLOYEE']['ID'] . ",',%')
							AND `subj_off`.`schlacadlvl_id` = " .$_POST['levelid'] . "
							AND `subj_off`.`schlacadyr_id`  = " .$_POST['yearid'] . "
							AND `subj_off`.`SchlEnrollSubjOff_STATUS` = 1
							AND `subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1
						GROUP BY `acad_prd`.`schlacadprd_name`,`acad_prd`.`schlacadprdsms_id`
						ORDER BY `acad_prd`.`SchlAcadPrd_RANKNO` DESC;";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		} else if ($_POST['type'] == 'ACADEXAMPERIOD'){
			//$qry = "CALL spGETacademicexaminationperiod(".$_POST['levelid'] .",".$_POST['yearid'] .",".$_POST['periodid'].")";
			//$rsreg = $dbConn->query($qry);
			//$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			$qry = "SELECT IFNULL(`SchlAcadExamPrd_CODE`,'') `CODE`,
						   IFNULL(`SchlAcadExamPrd_NAME`,'') `NAME`,
						   IFNULL(`SchlAcadExamPrd_DESC`,'') `DESC`,
						   IFNULL(`SchlAcadExamPrd_RANKNO`,0) `RANK_NO`,
						   IFNULL(`SchlAcadExamPrd_ID`,0) `ID`
						FROM `schoolacademicexaminationperiod`
					WHERE `SchlAcadExamPrd_STATUS` = 1
						AND `SchlAcadExamPrd_ISACTIVE` = 1
						AND `SchlAcadLvl_ID` = " . intval($_POST['levelid']) . "
						AND `SchlAcadYr_ID` = " . intval($_POST['yearid']) . "
						AND `SchlAcadPrd_ID` = " . intval($_POST['periodid']) . "
					ORDER BY `SchlAcadExamPrd_RANKNO`";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		} else if ($_POST['type'] == 'ACADCOURSE'){
			$qry = "SELECT `acad_crse`.`schlacadcrsesms_id`  `ID`,
										`acad_crse`.`schlacadcrses_name` `NAME`
						FROM `schoolenrollmentsubjectoffered` `subj_off` 
							LEFT JOIN `schoolacademiccourses` `acad_crse` 
							ON `subj_off`.`schlacadcrses_id` = `acad_crse`.`schlacadcrsesms_id` 
						WHERE CONCAT(',',`subj_off`.`schlprof_id`,',') LIKE CONCAT('%,', ". $_SESSION['EMPLOYEE']['ID'] . ",',%')
							AND `subj_off`.`schlacadlvl_id` = " .$_POST['levelid'] . "
							AND `subj_off`.`schlacadyr_id` = " .$_POST['yearid'] . "
							AND `subj_off`.`SchlAcadPrd_ID` = " .$_POST['periodid'] . "
							AND `subj_off`.`SchlEnrollSubjOff_STATUS` = 1
							AND `subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1
						GROUP BY `acad_crse`.`schlacadcrses_name`,`acad_crse`.`schlacadcrsesms_id`
						ORDER BY `acad_crse`.`schlacadcrses_name`;";
							
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		} else if ($_POST['type'] == 'OFFERED_SUBJECT'){
			$qry = "CALL spGETSubjectOffered(".$_SESSION['EMPLOYEE']['ID'].",".$_POST['levelid'] .",".$_POST['yearid'] .",".$_POST['periodid'] .",".$_POST['courseid'].")";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			//$rsreg->free_result();
			//$dbConn->close();
			//echo json_encode($fetch);
		} else if ($_POST['type'] == 'STUDENT_LIST'){
			// $schlacadlvl_id = intval($_POST['levelid']);  
			// $schlacadyr_id = intval($_POST['yearid']);  
			// $schlacadprd_id = intval($_POST['periodid']);  
			// $schlsubjoffered_id = intval($_POST['subjofferedid']);  
			
			// $qry = "CALL spGETstudentexaminationpermit(".$schlacadlvl_id.",".$schlacadyr_id.",".$schlacadprd_id.",".$schlsubjoffered_id.")";
			// $rsreg = $dbConn->query($qry);
			// $fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			$qry = "SELECT IFNULL(`ass`.`SchlStud_ID`,0) `ID`,
							CONCAT(IFNULL(`info`.`SchlEnrollRegStudInfo_LAST_NAME`,''), ', ' ,
								   IFNULL(`info`.`SchlEnrollRegStudInfo_FIRST_NAME`,''), ' ' ,
								   IFNULL(`info`.`SchlEnrollRegStudInfo_MIDDLE_NAME`,''), ' ' ,
								   IFNULL(`info`.`SchlEnrollRegStudInfo_SUFFIX_NAME`,'')
							) `NAME`,
							IFNULL(`info`.`SchlEnrollRegStudInfo_GENDER`,'') `GENDER`,
							IFNULL(`sec`.`schlacadsec_name`,'') `SECTION`,
							(SELECT CASE COUNT(`SchlEnrollAssSms_ID`)
								   WHEN 0 THEN 
									CASE IFNULL(`ass`.`SchlEnrollWithdrawType_ID`,0)
										WHEN 0 THEN 'ENROLLED'
										ELSE
											(SELECT IFNULL(`SchlEnrollWithdrawType_NAME`,'') 
												FROM `SchoolEnrollmentWithdrawalType`
											 WHERE IFNULL(`SchlEnrollWithdrawType_STATUS`,0) = 1
												AND IFNULL(`SchlEnrollWithdrawType_ISACTIVE`,0) = 1
												AND `SchlEnrollWithdrawType_ID` = `ass`.`SchlEnrollWithdrawType_ID`)
									END
								   ELSE 'DROP'
								END 
							  FROM `schoolenrollmentassessment` 
								WHERE `SchlEnrollAssSms_ID` = `ass`.`SchlEnrollAssSms_ID`
									AND CONCAT(',',`SchlAcadDropSubj_ID`,',') 
									LIKE CONCAT('%,'," . $_POST['subjofferedid'] . ",',%')
							) `STATUS`,
							IFNULL((SELECT GROUP_CONCAT(
									IFNULL(CONCAT(IFNULL(`examprd`.`SchlAcadExamPrdSms_ID`,0),'=',
										   IFNULL(`examprd`.`SchlAcadExamPrd_NAME`,''),'=',
										   CASE IFNULL(`promi`.`SchlEnrollExamPermitPromiSms_ID`,0)
											WHEN 0 THEN 0
											ELSE
											1
										END
									)
								,'')
							)
							  FROM `schoolacademicexaminationperiod` `examprd`
								LEFT JOIN `SchoolEnrollmentExaminationPermitPromissory` `promi`
									ON `examprd`.`SchlAcadExamPrdSms_ID` = `promi`.`SchlAcadExamPrd_ID`
								WHERE `examprd`.`SchlAcadExamPrd_STATUS` = 1
									AND `examprd`.`SchlAcadExamPrd_ISACTIVE` = 1
									AND `examprd`.`SchlAcadLvl_ID` = `ass`.`schlacadlvl_id`
									AND `examprd`.`SchlAcadYr_ID` = `ass`.`SchlAcadYr_ID`
									AND `examprd`.`SchlAcadPrd_ID` = `ass`.`SchlAcadPrd_ID`
									AND `promi`.`SchlEnrollAssColl_ID` = `ass`.`SchlEnrollAssSms_ID`
							ORDER BY `examprd`.`SchlAcadExamPrd_RANKNO`), '') `EXAMPERMIT_PRD_PROMI`,
							(CASE IFNULL(`exampermit`.`SchlAcadExamPrd_ID`,'')
								WHEN '' THEN 
								  (
									  SELECT IFNULL(GROUP_CONCAT(
										CONCAT(`SchlAcadExamPrdSms_ID`,'=-1')
										 ),'')
									FROM `schoolacademicexaminationperiod`
									  WHERE `SchlAcadLvl_ID` = `ass`.`schlacadlvl_id`
									AND `SchlAcadYr_ID`  = `ass`.`schlacadyr_id`
									AND `SchlAcadPrd_ID` = `ass`.`SchlAcadPrd_ID`
									AND `SchlAcadExamPrd_STATUS` = 1
									AND `SchlAcadExamPrd_ISACTIVE` = 1
									  ORDER BY `SchlAcadExamPrd_RANKNO`
								 )
								ELSE 
									IFNULL(`exampermit`.`SchlAcadExamPrd_ID`,'')
							END) `ENROLLEXAMPERMITPRD_ID`,
							IFNULL(`ass`.`SchlEnrollAssSms_ID`,0) `ASS_ID`
						FROM `schoolenrollmentassessment` `ass`
							LEFT JOIN `schoolenrollmentadmission` `adm`
								ON `ass`.`SchlEnrollAdm_ID` = `adm`.`SchlEnrollAdmSms_ID`
							LEFT JOIN `schoolenrollmentregistrationstudentinformation` `info`
								ON `adm`.`SchlEnrollReg_ID` = `info`.`SchlEnrollReg_ID`
							LEFT JOIN `SchoolEnrollmentExaminationPermit` `exampermit`
								ON `ass`.`SchlEnrollAssSms_ID` = `exampermit`.`SchlEnrollAssColl_ID`
							LEFT JOIN `schoolacademicsection` `sec`
								ON `ass`.`SchlAcadSec_ID` = `sec`.`SchlAcadSecSms_ID`
						WHERE CONCAT(',',`ass`.`SchlAcadSubj_ID`,',') LIKE CONCAT('%,'," . $_POST['subjofferedid'] . ",',%') 
							AND `ass`.`schlacadlvl_id`=  " . intval($_POST['levelid']) . "
							AND `ass`.`schlacadyr_id`= " . intval($_POST['yearid']) . "
							AND `ass`.`SchlAcadPrd_ID`= " . intval($_POST['periodid']) . "
							AND `ass`.`SchlEnrollAss_STATUS`= 1 
							AND `adm`.`SchlEnrollAdm_STATUS`= 1 
						ORDER BY `NAME`";
							
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		}
			
		//$rsreg->free_result();
		$dbConn->close();
		echo json_encode($fetch);
		//}
	}
?>