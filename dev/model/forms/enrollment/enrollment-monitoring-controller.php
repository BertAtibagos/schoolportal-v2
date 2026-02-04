<?php
	session_start();
	require_once '../../configuration/connection-config.php';
	//include '../../../configuration/connection-config.php';
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


	
	if ($_GET['type'] == 'ACADLEVEL'){
		$qry = "SELECT `SchlAcadLvlSms_ID` `ID`, `SchlAcadLvl_NAME` `NAME`
				FROM `schoolacademiclevel` ORDER BY `NAME` DESC";
		$rsreg = $dbConn->query($qry);	
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);

	} else if ($_GET['type'] == 'ACADYEAR'){

		$qry ="SELECT DISTINCT acadyr.`SchlAcadYrSms_ID` `ID`, acadyr.`SchlAcadYr_NAME` `NAME`
				FROM `schoolacademicyear` acadyr
				LEFT JOIN `schoolenrollmentsubjectoffered` `subj_off`
				ON acadyr.`SchlAcadYrSms_ID` = subj_off.`SchlAcadYr_ID`
				WHERE acadyr.`SchlAcadLvl_ID` = " .$_GET['levelid'] . " 
				AND subj_off.`SchlEnrollSubjOff_STATUS` = 1
				AND subj_off.`SchlEnrollSubjOff_ISACTIVE` = 1
				ORDER BY `SchlAcadYr_RANKNO` DESC";
		$rsreg = $dbConn->query($qry);
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		
	} else if ($_GET['type'] == 'ACADPERIOD'){
		$qry = "SELECT DISTINCT prd.`SchlAcadPrdSms_ID` `ID` ,
				prd.`SchlAcadPrd_NAME` `NAME`
				FROM `schoolacademicperiod` prd
				LEFT JOIN  `schoolenrollmentsubjectoffered` `subj_off`
				ON prd.`SchlAcadPrdSms_ID` = subj_off.`SchlAcadPrd_ID`
				LEFT JOIN schoolacademicyear acadyr
				ON subj_off.`SchlAcadYr_ID` = acadyr.`SchlAcadYrSms_ID`
				WHERE subj_off.`SchlAcadLvl_ID` =  " .$_GET['levelid'] . " 
				AND acadyr.`SchlAcadYrSms_ID` = " .$_GET['yearid'] . "
				AND subj_off.`SchlEnrollSubjOff_STATUS` = 1
				AND subj_off.`SchlEnrollSubjOff_ISACTIVE` = 1
				ORDER BY prd.`SchlAcadPrd_RANKNO`, `NAME`";
		$rsreg = $dbConn->query($qry);
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		
	} else if ($_GET['type'] == 'ACADCOURSE'){
		$qry = "SELECT DISTINCT `crse`.`SchlAcadCrseSms_ID` `ID`,
					`crse`.`SchlAcadCrses_NAME` `NAME`,
					`crse`.`SchlAcadCrses_CODE` `CODE`
				FROM `schooldepartment` `dept`
					LEFT JOIN `schoolacademiccourses` `crse`
						ON `dept`.`SchlDeptSms_ID` = `crse`.`SchlDept_ID`
					LEFT JOIN `schoolenrollmentsubjectoffered` `subj_off`
						ON `crse`.`SchlAcadCrseSms_ID` = `subj_off`.`SchlAcadCrses_ID` 
					LEFT JOIN `schoolacademiccourses` `crse1`
						ON `subj_off`.`SchlAcadCrses_ID` = `crse1`.`SchlAcadCrseSms_ID`
				WHERE `subj_off`.`SchlEnrollSubjOff_STATUS` = 1
					AND `subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1
					AND `subj_off`.`SchlAcadLvl_ID` = " .$_GET['levelid'] . "  " . "
					AND `subj_off`.`SchlAcadYr_ID` = " .$_GET['yearid'] . " " . "
					-- AND `dept`.`SchlDeptHead_ID`= ". $_SESSION['USERID'] . " " . "
					AND `subj_off`.`SchlAcadPrd_ID` = " .$_GET['periodid'] . " " . "
				ORDER BY `NAME`";
		$rsreg = $dbConn->query($qry);
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC); 
	} else if ($_GET['type'] == 'ENROLLMENTSTUDENTCOUNT') {
		$qry = "CALL  spGETenrollmentCount(".$_GET['yearid'] . "," 
											.$_GET['levelid'] . ","
											.$_GET['periodid'] . ");";
	
		$rsreg = $dbConn->query($qry);
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
	}

	  else if ($_GET['type'] == 'TOTAL_ENROLLED_STUDENT'){
		$SchlAcadLvl_ID = intval($_GET['levelid']);  
		$SchlAcadYr_ID = intval($_GET['yearid']);  
		$SchlAcadPrd_ID = intval($_GET['periodid']);
		// $SchlAcadCrse_ID = intval($_GET['courseid']);
		// $SchlAcadYrLvl_ID = intval($_GET['yearlevelid']);
		// $SchlDeptHead_ID = intval($_GET['headid']);
		// $SchlAcadSubj_ID = intval($_GET['offeredsubjid']);
		// $Categorytype = intval($_GET['categorytype']);
				
		$qry = "CALL spGETenrollmentsummary(".$SchlAcadLvl_ID.",".$SchlAcadYr_ID.",".$SchlAcadPrd_ID.",".$SchlAcadCrse_ID.",".$SchlAcadYrLvl_ID.",".$SchlDeptHead_ID.",".$SchlAcadSubj_ID.",".$Categorytype.");";
		$rsreg = $dbConn->query($qry);
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);

	} 

	
	$rsreg->free_result();
	$dbConn->close();
	echo json_encode($fetch);

?>