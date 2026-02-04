<?php
	session_start();
	include_once '../../../configuration/connection-config.php';
	if(isset($_POST['type'])){
        if($_POST['type'] == 'ACADLEVEL'){
			$qry = $dbConn->prepare("SELECT IFNULL(lvl.`SchlAcadLvlSms_ID`, 0) `ID`,
                    COALESCE(lvl.`SchlAcadLvl_NAME`, 'NO YEAR') AS `NAME`

                    FROM (
                            SELECT DISTINCT ass.`SchlAcadLvl_ID` 
                            FROM `schoolenrollmentassessment` ass 
                            WHERE ass.`SchlEnrollAss_STATUS` = 1
                    ) AS tbl

                    LEFT JOIN `schoolacademiclevel` lvl
                    ON tbl.`SchlAcadLvl_ID` = lvl.`SchlAcadLvlSms_ID`

                    ORDER BY `NAME` DESC
			");
            // $stmt->bind_param("ss", $username, $password); 
            $qry->execute();
            $rsreg = $qry->get_result();
            $fetch = $rsreg->fetch_all(MYSQLI_ASSOC); 
			
		} else if ($_POST['type'] == 'ACADYEAR'){
            $levelid = intval($_POST['levelid']);

            $qry = $dbConn->prepare("SELECT IFNULL(yr.`SchlAcadYrSms_ID`, 0) `ID`,
                    COALESCE(yr.`SchlAcadYr_NAME`, 'NO YEAR') AS `NAME`

                    FROM (
                            SELECT DISTINCT ass.`SchlAcadYr_ID` 
                            FROM `schoolenrollmentassessment` ass 
                            WHERE ass.`SchlEnrollAss_STATUS` = 1
                            AND ass.`SchlAcadLvl_ID` = ?
                    ) AS tbl

                    LEFT JOIN `schoolacademicyear` yr
                    ON tbl.`SchlAcadYr_ID` = yr.`SchlAcadYrSms_ID`

                    ORDER BY `ID` DESC
			");
            $qry->bind_param("i", $levelid); 
            $qry->execute();
            $rsreg = $qry->get_result();
            $fetch = $rsreg->fetch_all(MYSQLI_ASSOC); 

		} else if ($_POST['type'] == 'ACADPERIOD'){
            $levelid = intval($_POST['levelid']);
            $yearid = intval($_POST['yearid']);

			$qry = $dbConn->prepare("SELECT IFNULL(prd.`SchlAcadPrdSms_ID`, 0) `ID`,
                                COALESCE(prd.`SchlAcadPrd_NAME`, 'NO PERIOD') AS `NAME`

                                FROM (
                                        SELECT DISTINCT ass.`SchlAcadPrd_ID` 
                                        FROM `schoolenrollmentassessment` ass 
                                        WHERE ass.`SchlEnrollAss_STATUS` = 1
                                        AND ass.`SchlAcadLvl_ID` = ?
                                        AND ass.`SchlAcadYr_ID` = ?
                                ) AS tbl

                                LEFT JOIN `schoolacademicperiod` prd
                                ON tbl.`SchlAcadPrd_ID` = prd.`SchlAcadPrdSms_ID`

                                ORDER BY `ID` ASC;
			");
            $qry->bind_param("ii", $levelid, $yearid); 
            $qry->execute();
            $rsreg = $qry->get_result();
            $fetch = $rsreg->fetch_all(MYSQLI_ASSOC); 

		} else if ($_POST['type'] == 'ACADYEARLEVEL'){
            $levelid = intval($_POST['levelid']);
            $yearid = intval($_POST['yearid']);
            $periodid = intval($_POST['periodid']);

			$qry = $dbConn->prepare("SELECT IFNULL(yrlvl.`SchlAcadYrLvlSms_ID`, 0) `ID`,
                    COALESCE(yrlvl.`SchlAcadYrLvl_NAME`, 'NO YEAR LEVEL') AS `NAME`

                    FROM (
                        SELECT DISTINCT ass.`SchlAcadYrLvl_ID` 
                        FROM `schoolenrollmentassessment` ass 
                        WHERE ass.`SchlEnrollAss_STATUS` = 1
                        AND ass.`SchlAcadLvl_ID` = ?
                        AND ass.`SchlAcadYr_ID` = ?
                        AND ass.`SchlAcadPrd_ID` = ?
                    ) AS tbl

                    LEFT JOIN `schoolacademicyearlevel` yrlvl
                    ON tbl.`SchlAcadYrLvl_ID` = yrlvl.`SchlAcadYrLvlSms_ID`

                    ORDER BY `ID` ASC;
			");
            $qry->bind_param("iii", $levelid, $yearid, $periodid); 
            $qry->execute();
            $rsreg = $qry->get_result();
            $fetch = $rsreg->fetch_all(MYSQLI_ASSOC); 

		} else if ($_POST['type'] == 'ACADCOURSE'){
            $levelid = intval($_POST['levelid']);
            $yearid = intval($_POST['yearid']);
            $periodid = intval($_POST['periodid']);
            $yearlevelid = array_map('intval',explode(',', mysqli_real_escape_string($dbConn, $_POST['yearlevelid'])));

            $in_yrlvl = implode(',', array_fill(0, count($yearlevelid), '?'));

			$qry = $dbConn->prepare("SELECT IFNULL(crse.`SchlAcadCrseSms_ID`, 0) `ID`,
                    COALESCE(crse.`SchlAcadCrses_NAME`, 'NO COURSE') AS `NAME`

                    FROM (
                        SELECT DISTINCT ass.`SchlAcadCrse_ID` 
                        FROM `schoolenrollmentassessment` ass 
                        WHERE ass.`SchlEnrollAss_STATUS` = 1
                        AND ass.`SchlAcadLvl_ID` = ?
                        AND ass.`SchlAcadYr_ID` = ?
                        AND ass.`SchlAcadPrd_ID` = ?
                        AND ass.`SchlAcadYrLvl_ID` IN ($in_yrlvl)
                    ) AS tbl

                    LEFT JOIN `schoolacademiccourses` crse
                    ON tbl.`SchlAcadCrse_ID` = crse.`SchlAcadCrseSms_ID`

                    ORDER BY `NAME` ASC
			");
            
            $types = "iii" . str_repeat('i', count($yearlevelid));
            $params = array_merge([$levelid, $yearid, $periodid], $yearlevelid);

            $qry->bind_param($types, ...$params); 
            $qry->execute();
            $rsreg = $qry->get_result();
            $fetch = $rsreg->fetch_all(MYSQLI_ASSOC);

		} else if ($_POST['type'] == 'ACADSECTION'){
            $levelid = intval($_POST['levelid']);
            $yearid = intval($_POST['yearid']);
            $periodid = intval($_POST['periodid']);
            $yearlevelid = array_map('intval',explode(',', mysqli_real_escape_string($dbConn, $_POST['yearlevelid'])));
            $courseid = array_map('intval',explode(',', mysqli_real_escape_string($dbConn, $_POST['courseid'])));

            $in_yrlvl = implode(',', array_fill(0, count($yearlevelid), '?'));
            $in_crse = implode(',', array_fill(0, count($courseid), '?'));

			$qry = $dbConn->prepare("SELECT IFNULL(sec.`SchlAcadSecSms_ID`, 0) `ID`,
                    COALESCE(sec.`SchlAcadSec_NAME`, 'NO SECTION') AS `NAME`

                    FROM (
                        SELECT DISTINCT ass.`SchlAcadSec_ID` 
                        FROM `schoolenrollmentassessment` ass 
                        WHERE ass.`SchlEnrollAss_STATUS` = 1
                        AND ass.`SchlAcadLvl_ID` = ?
                        AND ass.`SchlAcadYr_ID` = ?
                        AND ass.`SchlAcadPrd_ID` = ?
                        AND ass.`SchlAcadYrLvl_ID` IN ($in_yrlvl)
                        AND ass.`SchlAcadCrse_ID` IN ($in_crse)
                    ) AS tbl

                    LEFT JOIN `schoolacademicsection` sec
                    ON tbl.`SchlAcadSec_ID` = sec.`SchlAcadSecSms_ID`

                    AND sec.`SchlAcadSec_STATUS` = 1
                    AND sec.`SchlAcadSec_ISACTIVE` = 1

                    ORDER BY `NAME` ASC
			");

            $types = "iii" . str_repeat('i', count($yearlevelid)) . str_repeat('i', count($courseid));
            $params = array_merge([$levelid, $yearid, $periodid], $yearlevelid, $courseid);

            $qry->bind_param($types, ...$params); 
            $qry->execute();
            $rsreg = $qry->get_result();
            $fetch = $rsreg->fetch_all(MYSQLI_ASSOC);

		} else if ($_POST['type'] == 'STUDENT_LIST'){
            $levelid = intval($_POST['levelid']);
            $yearid = intval($_POST['yearid']);
            $periodid = intval($_POST['periodid']);
            $yearlevelid = array_map('intval',explode(',', mysqli_real_escape_string($dbConn, $_POST['yearlevelid'])));
            $courseid = array_map('intval',explode(',', mysqli_real_escape_string($dbConn, $_POST['courseid'])));
            $sectionid = array_map('intval',explode(',', mysqli_real_escape_string($dbConn, $_POST['sectionid'])));
            $infotype = mysqli_real_escape_string($dbConn, $_POST['infotype']) == 'lastname' ? 'LAST_NAME' : 'FIRST_NAME';
            $infotext = mysqli_real_escape_string($dbConn, $_POST['infotext']);

            $in_yrlvl = implode(',', array_fill(0, count($yearlevelid), '?'));
            $in_crse = implode(',', array_fill(0, count($courseid), '?'));
            $in_sec = implode(',', array_fill(0, count($sectionid), '?'));

			$qry = $dbConn->prepare("SELECT DISTINCT 
                        ass.`SchlEnrollAssSms_ID` `ASS_ID`,
                        stud.`SchlStudSms_ID` `STUD_ID`,
                        stud.`SchlStud_NO` `STUD_NO`,
                        CONCAT(info.`SchlEnrollRegStudInfo_LAST_NAME`, ', ',
                            info.`SchlEnrollRegStudInfo_FIRST_NAME`, ' ',
                            info.`SchlEnrollRegStudInfo_MIDDLE_NAME`) `FULL_NAME`,
                        yrlvl.`SchlAcadYrLvl_NAME` `YRLVL_NAME`,
                        crse.`SchlAcadCrses_NAME` `CRSE_NAME`,
                        sec.`SchlAcadSec_NAME` `SEC_NAME`,
                        ass.`SchlAcadCrse_ID` `CRSE_ID`

                    FROM `schoolstudent` stud

                    LEFT JOIN `schoolenrollmentregistration` reg
                    ON stud.`SchlEnrollRegColl_ID` = reg.`SchlEnrollRegSms_ID`
                    LEFT JOIN `schoolenrollmentassessment` ass
                    ON stud.`SchlStudSms_ID` = ass.`SchlStud_ID`
                    LEFT JOIN `schoolenrollmentregistrationstudentinformation` info
                    ON reg.`SchlEnrollRegSms_ID` = info.`SchlEnrollReg_ID`
                    LEFT JOIN `schoolacademicyearlevel` yrlvl
                    ON ass.`SchlAcadYrLvl_ID` = yrlvl.`SchlAcadYrLvlSms_ID`
                    LEFT JOIN `schoolacademiccourses` crse
                    ON ass.`SchlAcadCrse_ID` = crse.`SchlAcadCrseSms_ID`
                    LEFT JOIN `schoolacademicsection` sec
                    ON ass.`SchlAcadSec_ID` = sec.`SchlAcadSecSms_ID`

					WHERE stud.`SchlStud_STATUS` = 1
					AND stud.`SchlStud_ISACTIVE` = 1
					AND reg.`SchlEnrollReg_STATUS` = 1
					AND ass.`SchlEnrollAss_STATUS` = 1
					AND ass.`SchlAcadLvl_ID` = ?
					AND ass.`SchlAcadYr_ID` = ?
					AND ass.`SchlAcadPrd_ID` = ?
					AND info.`SchlEnrollRegStudInfo_$infotype` LIKE CONCAT('%', ?, '%')
					AND ass.`SchlAcadYrLvl_ID` IN ($in_yrlvl)
					AND ass.`SchlAcadCrse_ID` IN ($in_crse)
					AND ass.`SchlAcadSec_ID` IN ($in_sec)
                    
					ORDER BY `FULL_NAME`
			");

            $types = "iiis" . str_repeat('i', count($yearlevelid)) . str_repeat('i', count($courseid)) . str_repeat('i', count($sectionid));
            $params = array_merge([$levelid, $yearid, $periodid, $infotext], $yearlevelid, $courseid, $sectionid);

            $qry->bind_param($types, ...$params);
            $qry->execute();
            $rsreg = $qry->get_result();
            $fetch = $rsreg->fetch_all(MYSQLI_ASSOC);
		}  else if ($_POST['type'] == 'GRADES'){
            $studid = isset($_POST['studid']) ? (int)$_POST['studid'] : die("POST NOT FOUND");
            $levelid = isset($_POST['levelid']) ? (int)$_POST['levelid'] : die("POST NOT FOUND");
            $yearid = isset($_POST['yearid']) ? (int)$_POST['yearid'] : die("POST NOT FOUND");
            $periodid = isset($_POST['periodid']) ? (int)$_POST['periodid'] : die("POST NOT FOUND");
            $courseid = isset($_POST['courseid']) ? (int)$_POST['courseid'] : die("POST NOT FOUND");

            $qry = $dbConn->prepare("CALL `spDisplayStudentGrades`(?,?,?,?,?,0,0,1);");
            $qry->bind_param('iiiii', $studid, $levelid, $yearid, $periodid, $courseid);
            $qry->execute();
            $rsreg = $qry->get_result();
            $fetch = $rsreg->fetch_all(MYSQLI_ASSOC);
		}  else if ($_POST['type'] == 'EQUIVALENT'){
            $levelid = isset($_POST['levelid']) ? (int)$_POST['levelid'] : die("POST NOT FOUND");
            $yearid = isset($_POST['yearid']) ? $_POST['yearid'] : die("POST NOT FOUND");
            $periodid = isset($_POST['periodid']) ? $_POST['periodid'] : die("POST NOT FOUND");

            $qry = $dbConn->prepare("SELECT ROUND(equiv.`SchlAcadGradeEquivalent`, 2) `EQUIV`,
                    CONCAT(IF(equiv.`SchlAcadGradeRangeFrom` > 100, ' above', equiv.`SchlAcadGradeRangeFrom`), ' - ', 
                            IF(equiv.`SchlAcadGradeRangeTo` = 0, ' below', equiv.`SchlAcadGradeRangeTo`)) `RANGE`

                    FROM `schoolacademicgradeequivalent` equiv

                    WHERE equiv.`SchlAcadGradeEqui_STATUS` = 1
                    AND equiv.`SchlAcadGradeEqui_IsActive` = 1
                    AND equiv.`SchlAcadLvl_ID` = ?
                    AND FIND_IN_SET(?, equiv.`SchlAcadYr_ID`)
                    AND FIND_IN_SET(?, equiv.`SchlAcadPrd_ID`)

                    ORDER BY `EQUIV` ASC");
            $qry->bind_param('iss', $levelid, $yearid, $periodid);
            $qry->execute();
            $rsreg = $qry->get_result();
            $fetch = $rsreg->fetch_all(MYSQLI_ASSOC);
        }
	}

	$rsreg->free_result();
	$dbConn->close();
	echo json_encode($fetch);
?>