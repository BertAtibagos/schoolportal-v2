<?php
	session_start();
	require_once '../configuration/connection-config.php';
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	if (isset($_POST['type'])){
		if ($_POST['type'] == 'CHECK_SUBMITTED_REQUEST_LIST')
		{
			$qry = "SELECT COUNT(`studrec`.`SchlStudAcadRec_ID`) `NOTIF`
						FROM `schoolstudentacademicrecord` `studrec`
					WHERE `studrec`.`SchlStudAcadRec_STATUS` = 1
						AND `studrec`.`SchlStudAcadRec_ISACTIVE` = 1
						AND `studrec`.`SchlStudAcadRec_REQ_STATUS` <> 0
						AND `studrec`.`SchlSign_ID`= ". $_SESSION['EMPLOYEE']['ID'];
			
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			$rsreg->free_result();
			$dbConn->close();
			echo json_encode($fetch);//$_SESSION['USERID'];//
		}
	}
?>