<?php
	session_start();
	require_once '../../configuration/connection-config.php';
	//include '../../../configuration/connection-config.php';
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	
	if ($_GET['type'] == 'GET_APPROVAL_REQUEST'){
		$qry = "SELECT COUNT(CASE ISNULL(`studrec`.`SchlStudAcadRec_ID`)
									WHEN 1 THEN 
										0
									ELSE
										`studrec`.`SchlStudAcadRec_ID`
								END) `CNT`
				FROM `schoolstudentacademicrecord` `studrec`
					LEFT JOIN `schoolenrollmentsubjectoffered` `subj_off`
						ON `studrec`.`SchlEnrollSubjOff_ID` = `subj_off`.`SchlEnrollSubjOffSms_ID`
				WHERE `subj_off`.`SchlEnrollSubjOff_STATUS` = 1
					AND `subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1
					AND `studrec`.`SchlStudAcadRec_STATUS` = 1
					AND `studrec`.`SchlStudAcadRec_ISACTIVE` = 1
					AND `studrec`.`SchlSign_ID`= ". $_SESSION['USERID'];
					
		$rsreg = $dbConn->query($qry);	
		$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
	} 
	
	$rsreg->free_result();
	$dbConn->close();
	echo json_encode($fetch);
?>