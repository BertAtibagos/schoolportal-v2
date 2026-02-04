<?php
	session_start();
	include_once '../../../configuration/connection-config.php';
	if(isset($_GET['type'])){
        if($_GET['type'] == 'ACADLEVEL'){
			$qry = "SELECT IFNULL(lvl.`SchlAcadLvlSms_ID`, 0) `ID`,
                                COALESCE(lvl.`SchlAcadLvl_NAME`, 'NO YEAR') AS `NAME`

                                FROM (
                                        SELECT DISTINCT off.`SchlAcadLvl_ID`
                                        FROM `schoolenrollmentsubjectoffered` off
	
                                        LEFT JOIN `schoolacademiccourses` crse
                                        ON off.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID`
                                        LEFT JOIN `schooldepartment` dept
                                        ON crse.`SchlDept_ID` = dept.`SchlDeptSms_ID`
                                        WHERE off.`SchlEnrollSubjOff_STATUS` = 1
                                        AND off.`SchlEnrollSubjOff_ISACTIVE` = 1
                                        AND dept.`SchlDeptHead_ID` = " . $_SESSION['USERID'] . "
                                ) AS tbl

                                LEFT JOIN `schoolacademiclevel` lvl
                                ON tbl.`SchlAcadLvl_ID` = lvl.`SchlAcadLvlSms_ID`

                                ORDER BY `NAME` DESC
			";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
			
		} else if ($_GET['type'] == 'ACADYEAR'){
                        $qry = "SELECT IFNULL(yr.`SchlAcadYrSms_ID`, 0) `ID`,
                                COALESCE(yr.`SchlAcadYr_NAME`, 'NO YEAR') AS `NAME`

                                FROM (
                                        SELECT DISTINCT off.`SchlAcadYr_ID`
                                        FROM `schoolenrollmentsubjectoffered` off
	
                                        LEFT JOIN `schoolacademiccourses` crse
                                        ON off.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID`
                                        LEFT JOIN `schooldepartment` dept
                                        ON crse.`SchlDept_ID` = dept.`SchlDeptSms_ID`
                                        WHERE off.`SchlEnrollSubjOff_STATUS` = 1
                                        AND off.`SchlEnrollSubjOff_ISACTIVE` = 1
                                        AND dept.`SchlDeptHead_ID` = " . $_SESSION['USERID'] . "
                                        AND off.`SchlAcadLvl_ID` = ".$_GET['levelid']."
                                ) AS tbl

                                LEFT JOIN `schoolacademicyear` yr
                                ON tbl.`SchlAcadYr_ID` = yr.`SchlAcadYrSms_ID`

                                ORDER BY `ID` DESC
			";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);

		} else if ($_GET['type'] == 'ACADPERIOD'){
			$qry = "SELECT IFNULL(prd.`SchlAcadPrdSms_ID`, 0) `ID`,
                                COALESCE(prd.`SchlAcadPrd_NAME`, 'NO PERIOD') AS `NAME`

                                FROM (
                                        SELECT DISTINCT off.`SchlAcadPrd_ID`
                                        FROM `schoolenrollmentsubjectoffered` off
	
                                        LEFT JOIN `schoolacademiccourses` crse
                                        ON off.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID`
                                        LEFT JOIN `schooldepartment` dept
                                        ON crse.`SchlDept_ID` = dept.`SchlDeptSms_ID`
                                        WHERE off.`SchlEnrollSubjOff_STATUS` = 1
                                        AND off.`SchlEnrollSubjOff_ISACTIVE` = 1
                                        AND dept.`SchlDeptHead_ID` = " . $_SESSION['USERID'] . "
                                        AND off.`SchlAcadLvl_ID` = ".$_GET['levelid']."
                                        AND off.`SchlAcadYr_ID` = ".$_GET['yearid']."
                                ) AS tbl

                                LEFT JOIN `schoolacademicperiod` prd
                                ON tbl.`SchlAcadPrd_ID` = prd.`SchlAcadPrdSms_ID`

                                ORDER BY `ID` DESC
			";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);

		} else if ($_GET['type'] == 'ACADYEARLEVEL'){
			$qry = "SELECT IFNULL(yrlvl.`SchlAcadYrLvlSms_ID`, 0) `ID`,
                                COALESCE(yrlvl.`SchlAcadYrLvl_NAME`, 'NO YEAR LEVEL') AS `NAME`

                                FROM (
                                        SELECT DISTINCT off.`SchlAcadYrLvl_ID`
                                        FROM `schoolenrollmentsubjectoffered` off
	
                                        LEFT JOIN `schoolacademiccourses` crse
                                        ON off.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID`
                                        LEFT JOIN `schooldepartment` dept
                                        ON crse.`SchlDept_ID` = dept.`SchlDeptSms_ID`
                                        WHERE off.`SchlEnrollSubjOff_STATUS` = 1
                                        AND off.`SchlEnrollSubjOff_ISACTIVE` = 1
                                        AND dept.`SchlDeptHead_ID` = " . $_SESSION['USERID'] . "
                                        AND off.`SchlAcadLvl_ID` = ".$_GET['levelid']."
                                        AND off.`SchlAcadYr_ID` = ".$_GET['yearid']."
                                        AND off.`SchlAcadPrd_ID` IN(".$_GET['periodid'].")
                                ) AS tbl

                                LEFT JOIN `schoolacademicyearlevel` yrlvl
                                ON tbl.`SchlAcadYrLvl_ID` = yrlvl.`SchlAcadYrLvlSms_ID`

                                ORDER BY `ID` ASC
			";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);

		} else if ($_GET['type'] == 'ACADCOURSE'){
			$qry = "SELECT IFNULL(crse.`SchlAcadCrseSms_ID`, 0) `ID`,
                                COALESCE(crse.`SchlAcadCrses_NAME`, 'NO COURSE') AS `NAME`

                                FROM (
                                        SELECT DISTINCT off.`SchlAcadCrses_ID`
                                        FROM `schoolenrollmentsubjectoffered` off
	
                                        LEFT JOIN `schoolacademiccourses` crse
                                        ON off.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID`
                                        LEFT JOIN `schooldepartment` dept
                                        ON crse.`SchlDept_ID` = dept.`SchlDeptSms_ID`
                                        WHERE off.`SchlEnrollSubjOff_STATUS` = 1
                                        AND off.`SchlEnrollSubjOff_ISACTIVE` = 1
                                        AND dept.`SchlDeptHead_ID` = " . $_SESSION['USERID'] . "
                                        AND off.`SchlAcadLvl_ID` = ".$_GET['levelid']."
                                        AND off.`SchlAcadYr_ID` = ".$_GET['yearid']."
                                        AND off.`SchlAcadPrd_ID` IN (".$_GET['periodid'].")
                                        AND off.`SchlAcadYrLvl_ID` IN (".$_GET['yearlevelid'].")
                                ) AS tbl

                                LEFT JOIN `schoolacademiccourses` crse
                                ON tbl.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID`

                                ORDER BY `NAME` ASC
			";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);

		} else if ($_GET['type'] == 'ACADSECTION'){
			$qry = "SELECT IFNULL(sec.`SchlAcadSecSms_ID`, 0) `ID`,
                                COALESCE(sec.`SchlAcadSec_NAME`, 'NO SECTION') AS `NAME`

                                FROM (
                                        SELECT DISTINCT off.`SchlAcadSec_ID`
                                        FROM `schoolenrollmentsubjectoffered` off
	
                                        LEFT JOIN `schoolacademiccourses` crse
                                        ON off.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID`
                                        LEFT JOIN `schooldepartment` dept
                                        ON crse.`SchlDept_ID` = dept.`SchlDeptSms_ID`
                                        WHERE off.`SchlEnrollSubjOff_STATUS` = 1
                                        AND off.`SchlEnrollSubjOff_ISACTIVE` = 1
                                        AND dept.`SchlDeptHead_ID` = " . $_SESSION['USERID'] . "
                                        AND off.`SchlAcadLvl_ID` = ".$_GET['levelid']."
                                        AND off.`SchlAcadYr_ID` = ".$_GET['yearid']."
                                        AND off.`SchlAcadPrd_ID` IN (".$_GET['periodid'].")
                                        AND off.`SchlAcadYrLvl_ID` IN (".$_GET['yearlevelid'].")
                                        AND off.`SchlAcadCrses_ID` IN (".$_GET['courseid'].")
                                        AND off.`SchlAcadSec_ID` > 0
                                ) AS tbl

                                LEFT JOIN `schoolacademicsection` sec
                                ON tbl.`SchlAcadSec_ID` = sec.`SchlAcadSecSms_ID`
                                AND sec.`SchlAcadSec_STATUS` = 1
                                AND sec.`SchlAcadSec_ISACTIVE` = 1

                                ORDER BY `NAME` ASC
			";
			$rsreg = $dbConn->query($qry);
			$fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);

		} else if ($_GET['type'] == 'STUDENT_LIST'){
			$qry = "SELECT stud.`SchlStudSms_ID` `STUDID`,
                        ass.`SchlEnrollAssSms_ID` `ASSID`,
                        stud.`SchlStud_IDNO` `STUDENT_ID_NUMBER`,
                        CONCAT(info.`SchlEnrollRegStudInfo_LAST_NAME`, ', ', info.`SchlEnrollRegStudInfo_FIRST_NAME` , ' ', info.`SchlEnrollRegStudInfo_MIDDLE_NAME`, ' ', info.`SchlEnrollRegStudInfo_SUFFIX_NAME`) `STUDENT_NAME`,
                        info.`SchlEnrollRegStudInfo_MOB_NO` `MOBILE`,
                        info.`SchlEnrollRegStudInfo_EMAIL_ADD` `EMAIL`,
                        info.`SchlEnrollRegStudInfo_GENDER` `GENDER`,
                        sec.`SchlAcadSec_CODE` `SECTION`,
                        yearlevel.`SchlAcadYrLvl_NAME` `YEAR_LEVEL`
                                
                        FROM `schoolenrollmentassessment` ass
                        LEFT JOIN `schoolstudent` stud
                        ON ass.`SchlStud_ID` = stud.`SchlStudSms_ID`
                        LEFT JOIN `schoolenrollmentregistrationstudentinformation` info
                        ON stud.`SchlEnrollRegColl_ID` = info.`SchlEnrollReg_ID`
                        LEFT JOIN `schoolacademicsection` sec
                        ON ass.`SchlAcadSec_ID` = sec.`SchlAcadSecSms_ID`
                        LEFT JOIN `schoolacademicyearlevel` yearlevel
                        ON ass.`SchlAcadYrLvl_ID` = yearlevel.`SchlAcadYrLvlSms_ID`

                        WHERE ass.`SchlEnrollAss_STATUS` = 1
                        AND ass.`SchlEnrollWithdrawType_ID` = 0
                        AND stud.`SchlStud_ISACTIVE` = 1
                        AND stud.`SchlStud_STATUS` = 1
                        and ass.`SchlAcadLvl_ID` = ".$_GET['levelid']."
                        and ass.`SchlAcadYr_ID` = ".$_GET['yearid']."
                        and ass.`SchlAcadPrd_ID` IN (".$_GET['periodid'].")
                        and ass.`SchlAcadYrLvl_ID` IN (".$_GET['yearlevelid'].")
                        and ass.`SchlAcadCrse_ID` IN (".$_GET['courseid'].")
                        and ass.`SchlAcadSec_ID` IN (".$_GET['sectionid'].")

                        HAVING (SELECT COUNT(DISTINCT inv.`SchlEnrollInvSms_ID`)
                                FROM `schoolenrollmentinvoice` inv
                                WHERE inv.`SchlEnrollInv_STATUS` = 1
                                AND inv.`SchlEnrollInv_ISACTIVE` = 1
                                AND inv.`SchlEnrollInv_ISCANCEL` = 0
                                AND inv.`SchlEnrollAss_ID` = `ASSID`
                                AND inv.`SchlStud_ID` = `STUDID`) > 0

                        ORDER BY `SECTION`, `STUDENT_NAME` ASC;
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