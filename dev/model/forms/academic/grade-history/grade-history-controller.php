<?php
	session_start();
	include_once '../../../configuration/connection-config.php';
	if(isset($_POST['type'])){
        if($_POST['type'] == 'ACADLEVEL'){
			$qry = "SELECT IFNULL(lvl.`SchlAcadLvlSms_ID`, 0) `ID`,
                                COALESCE(lvl.`SchlAcadLvl_NAME`, 'NO YEAR') AS `NAME`

                                FROM (
                                        SELECT DISTINCT off.`SchlAcadLvl_ID`
                                        FROM `schoolenrollmentsubjectoffered` off
                                        WHERE off.`SchlEnrollSubjOff_STATUS` = 1
                                        AND off.`SchlEnrollSubjOff_ISACTIVE` = 1 
                                ) AS tbl

                                LEFT JOIN `schoolacademiclevel` lvl
                                ON tbl.`SchlAcadLvl_ID` = lvl.`SchlAcadLvlSms_ID`

                                ORDER BY `NAME` DESC
			";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			
		} else if ($_POST['type'] == 'ACADYEAR'){
                        $qry = "SELECT IFNULL(yr.`SchlAcadYrSms_ID`, 0) `ID`,
                                COALESCE(yr.`SchlAcadYr_NAME`, 'NO YEAR') AS `NAME`

                                FROM (
                                        SELECT DISTINCT off.`SchlAcadYr_ID`
                                        FROM `schoolenrollmentsubjectoffered` off
                                        WHERE off.`SchlEnrollSubjOff_STATUS` = 1
                                        AND off.`SchlEnrollSubjOff_ISACTIVE` = 1 
                                        AND off.`SchlAcadLvl_ID` = ".$_POST['levelid']."
                                ) AS tbl

                                LEFT JOIN `schoolacademicyear` yr
                                ON tbl.`SchlAcadYr_ID` = yr.`SchlAcadYrSms_ID`

                                ORDER BY `ID` DESC
			";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);

		} else if ($_POST['type'] == 'ACADPERIOD'){
			$qry = "SELECT IFNULL(prd.`SchlAcadPrdSms_ID`, 0) `ID`,
                                COALESCE(prd.`SchlAcadPrd_NAME`, 'NO PERIOD') AS `NAME`

                                FROM (
                                        SELECT DISTINCT off.`SchlAcadPrd_ID`
                                        FROM `schoolenrollmentsubjectoffered` off
                                        WHERE off.`SchlEnrollSubjOff_STATUS` = 1
                                        AND off.`SchlEnrollSubjOff_ISACTIVE` = 1 
                                        AND off.`SchlAcadLvl_ID` = ".$_POST['levelid']."
                                        AND off.`SchlAcadYr_ID` = ".$_POST['yearid']."
                                ) AS tbl

                                LEFT JOIN `schoolacademicperiod` prd
                                ON tbl.`SchlAcadPrd_ID` = prd.`SchlAcadPrdSms_ID`

                                ORDER BY `ID` ASC
			";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);

		} else if ($_POST['type'] == 'ACADYEARLEVEL'){
			$qry = "SELECT IFNULL(yrlvl.`SchlAcadYrLvlSms_ID`, 0) `ID`,
                                COALESCE(yrlvl.`SchlAcadYrLvl_NAME`, 'NO YEAR LEVEL') AS `NAME`

                                FROM (
                                        SELECT DISTINCT off.`SchlAcadYrLvl_ID`
                                        FROM `schoolenrollmentsubjectoffered` off
                                        WHERE off.`SchlEnrollSubjOff_STATUS` = 1
                                        AND off.`SchlEnrollSubjOff_ISACTIVE` = 1 
                                        AND off.`SchlAcadLvl_ID` = ".$_POST['levelid']."
                                        AND off.`SchlAcadYr_ID` = ".$_POST['yearid']."
                                        AND off.`SchlAcadPrd_ID` = ".$_POST['periodid']."
                                ) AS tbl

                                LEFT JOIN `schoolacademicyearlevel` yrlvl
                                ON tbl.`SchlAcadYrLvl_ID` = yrlvl.`SchlAcadYrLvlSms_ID`

                                ORDER BY `ID` ASC
			";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);

		} else if ($_POST['type'] == 'ACADCOURSE'){
			$qry = "SELECT IFNULL(crse.`SchlAcadCrseSms_ID`, 0) `ID`,
                                COALESCE(crse.`SchlAcadCrses_NAME`, 'NO COURSE') AS `NAME`

                                FROM (
                                        SELECT DISTINCT off.`SchlAcadCrses_ID`
                                        FROM `schoolenrollmentsubjectoffered` off
                                        WHERE off.`SchlEnrollSubjOff_STATUS` = 1
                                        AND off.`SchlEnrollSubjOff_ISACTIVE` = 1 
                                        AND off.`SchlAcadLvl_ID` = ".$_POST['levelid']."
                                        AND off.`SchlAcadYr_ID` = ".$_POST['yearid']."
                                        AND off.`SchlAcadPrd_ID` = ".$_POST['periodid']."
                                        AND off.`SchlAcadYrLvl_ID` = ".$_POST['yearlevelid']."
                                ) AS tbl

                                LEFT JOIN `schoolacademiccourses` crse
                                ON tbl.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID`

                                ORDER BY `NAME` ASC
			";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);

		} else if ($_POST['type'] == 'ACADSECTION'){
			$qry = "SELECT IFNULL(sec.`SchlAcadSecSms_ID`, 0) `ID`,
                                COALESCE(sec.`SchlAcadSec_NAME`, 'NO SECTION') AS `NAME`

                                FROM (
                                        SELECT DISTINCT off.`SchlAcadSec_ID`
                                        FROM `schoolenrollmentsubjectoffered` off
                                        WHERE off.`SchlEnrollSubjOff_STATUS` = 1
                                        AND off.`SchlEnrollSubjOff_ISACTIVE` = 1 
                                        AND off.`SchlAcadLvl_ID` = ".$_POST['levelid']."
                                        AND off.`SchlAcadYr_ID` = ".$_POST['yearid']."
                                        AND off.`SchlAcadPrd_ID` = ".$_POST['periodid']."
                                        AND off.`SchlAcadYrLvl_ID` = ".$_POST['yearlevelid']."
                                        AND off.`SchlAcadCrses_ID` = ".$_POST['courseid']."
                                ) AS tbl

                                LEFT JOIN `schoolacademicsection` sec
                                ON tbl.`SchlAcadSec_ID` = sec.`SchlAcadSecSms_ID`
                                AND sec.`SchlAcadSec_STATUS` = 1
                                AND sec.`SchlAcadSec_ISACTIVE` = 1

                                ORDER BY `ID` ASC
			";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);

		} else if ($_POST['type'] == 'SUBJECT_LIST'){
			$qry = "SELECT tbl.`SchlEnrollSubjOffSms_ID` `OFF_ID`,
                                subj.`SchlAcadSubj_CODE` `CODE`,
                                subj.`SchlAcadSubj_NAME` `DESCRIPTION`,
                                tbl.`SchlEnrollSubjOff_UNIT` `UNIT`,
                                tbl.`SchlEnrollSubjOff_SCHEDULE_2` `SCHEDULE`,
                                COALESCE(CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`), 'NO PROFESSOR') AS `INSTRUCTOR`

                                FROM (SELECT off.`SchlEnrollSubjOffSms_ID`,
                                        off.`SchlEnrollSubjOff_UNIT`,
                                        off.`SchlEnrollSubjOff_SCHEDULE_2`,
                                        off.`SchlAcadSubj_ID`,
                                        off.`SchlProf_ID`

                                        FROM `schoolenrollmentsubjectoffered` off

                                        WHERE off.`SchlEnrollSubjOff_STATUS` = 1
                                        AND off.`SchlEnrollSubjOff_ISACTIVE` = 1
                                        AND off.`SchlAcadLvl_ID` = ".$_POST['levelid']."
                                        AND off.`SchlAcadYr_ID` = ".$_POST['yearid']."
                                        AND off.`SchlAcadPrd_ID` = ".$_POST['periodid']."
                                        AND off.`SchlAcadYrLvl_ID` = ".$_POST['yearlevelid']."
                                        AND off.`SchlAcadCrses_ID` = ".$_POST['courseid']."
                                        AND off.`SchlAcadSec_ID` = ".$_POST['sectionid']."
                                ) AS tbl

                                LEFT JOIN `schoolacademicsubject` subj
                                ON tbl.`SchlAcadSubj_ID` = subj.`SchlAcadSubjSms_ID`
                                LEFT JOIN `schoolemployee` emp
                                ON tbl.`SchlProf_ID` = emp.`SchlEmpSms_ID`
                                
                                ORDER BY `CODE` ASC
			";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);

		} else if ($_POST['type'] == 'GET_HISTORY'){
			$qry = "SELECT his.`SchlStudAcadRecAppHis_ISAPPROVED` `ISAPPOVED`,
                                CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`) `APPROVER`,
                                his.`SchlStudAcadRecAppHis_REQ_STATUS` `REQ_STATUS`,
                                rec.`SchlStudAcadRec_REQ_STATUS` `CURRENT_STATUS`,
                                his.`SchlStudAcadRecAppHis_DATETIME` `DATE`

                                FROM `schoolstudentacademicrecordapprovalhistory` his

                                LEFT JOIN `schoolstudentacademicrecord` rec
                                ON his.`SchlEnrollSubjOff_ID` = rec.`SchlEnrollSubjOff_ID` 
                                        AND his.`SchlStudAcadRec_ID` = rec.`SchlStudAcadRec_ID` 
                                LEFT JOIN `schoolemployee` emp
                                ON his.`SchlSign_ID` = emp.`SchlEmpSms_ID`

                                WHERE his.`SchlStudAcadRecAppHis_ISACTIVE` = 1
                                AND his.`SchlStudAcadRecAppHis_STATUS` = 1
                                AND rec.`SchlStudAcadRec_STATUS` = 1
                                AND rec.`SchlStudAcadRec_ISACTIVE` = 1
                                AND emp.`SchlEmp_STATUS` = 1
                                AND emp.`SchlEmp_ISACTIVE` = 1
                                AND his.`SchlEnrollSubjOff_ID` = ".$_POST['subjid']."
			";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			
		} else {
			echo '<script>alert("POST CHANGED. CONTACT ICT DEPARTMENT")</script>';
		}
		
	} else {
    	echo '<script>alert("POST NOT SET. CONTACT ICT DEPARTMENT")</script>';
	}

	$rsreg->free_result();
	$dbConn->close();
	echo json_encode($fetch);
?>