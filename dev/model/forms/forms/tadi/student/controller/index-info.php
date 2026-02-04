<?php 
// ✅ Secure cookie flags (must be set before session_start)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');

// PHP error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
include('../../../../../configuration/connection-config.php');

$fetch = "";

$type = $_POST['type'] ?? '';

if ($type === 'GET_SCHOOL_YEAR') {

    $qry = "SELECT `SchlAcadYr_NAME` AS `AcadYr_Name`,
                   `SchlAcadYr_DESC` AS `AcadYr_Desc`,
                   `SchlAcadYrSms_ID` AS `AcadYr_ID`
            FROM `schoolacademicyear`
            WHERE `SchlAcadYr_ID` = " . $_SESSION['STUDENT']['YRID'] . "
            AND `SchlAcadYr_STATUS` = 1 
            AND`SchlAcadYr_ISACTIVE` = 1";

    $rreg = $dbConn->query($qry);
    $fetch = $rreg->fetch_all(MYSQLI_ASSOC);
}

if ($type === 'GET_ACADEMIC_PRD') {

    $qry = "SELECT `SchlAcadPrd_NAME` AS `Period_Name`,
                   `SchlAcadPrd_DESC` AS `Period_Desc`,
                   `SchlAcadPrdSms_ID` AS `Period_ID`
            FROM `schoolacademicperiod`
            WHERE `SchlAcadPrdSms_ID` = " . $_SESSION['STUDENT']['PRDID'] . "
            AND `SchlAcadPrd_STATUS` = 1
            AND `SchlAcadPrd_ISACTIVE` = 1";

    $rreg = $dbConn->query($qry);
    $fetch = $rreg->fetch_all(MYSQLI_ASSOC);
}

if ($type === 'GET_YEAR_LEVEL') {

    $qry = "SELECT `SchlAcadYrLvl_NAME` AS `Yrlvl_Name`,
                   `SchlAcadYrLvl_DESC` AS `Yrlvl_Desc`,
                   `SchlAcadYrLvlSms_ID` AS `Yrlvl_ID`
            FROM `schoolacademicyearlevel`
            WHERE `SchlAcadYrLvlSms_ID` = " . $_SESSION['STUDENT']['YRLVLID'] . " 
            AND `SchlAcadYrLvl_STATUS` = 1 
            AND `SchlAcadYrLvl_ISACTIVE` = 1";

    $rreg = $dbConn->query($qry);
    $fetch = $rreg->fetch_all(MYSQLI_ASSOC);
}

if ($type === 'GET_ACADEMIC_LEVEL') {

    $qry = "SELECT `SchlAcadLvl_NAME` AS `AcadLvl_Name`,
                   `SchlAcadLvl_DESC` AS `AcadLvl_Desc`,
                   `SchlAcadLvlSms_ID` AS `AcadLvl_ID`
            FROM `schoolacademiclevel`
            WHERE `SchlAcadLvlSms_ID` =  " . $_SESSION['STUDENT']['LVLID'] . " 
            AND `SchlAcadLvl_STATUS` = 1 
            AND `SchlAcadLvl_ISACTIVE` = 1";

    $rreg = $dbConn->query($qry);
    $fetch = $rreg->fetch_all(MYSQLI_ASSOC);
}

if ($type === 'GET_SUBJECT_LIST') {

    $USERID = $_SESSION['STUDENT']['ID'];
    $LVLID  = $_SESSION['STUDENT']['LVLID'];
    $YRID   = $_SESSION['STUDENT']['YRID']; 
    $PRDID  = $_SESSION['STUDENT']['PRDID']; 

    $qry_get_subj_list = "SELECT `schl_enr_as`.`SchlAcadSubj_ID` AS `schl_acad_subj_id`
                          FROM `schoolstudent` `schl_stud`
                          LEFT JOIN `schoolenrollmentregistration` `schl_enr_reg`
                            ON `schl_stud`.`SchlEnrollRegColl_ID` = `schl_enr_reg`.`SchlEnrollRegSms_ID`
                          LEFT JOIN `schoolenrollmentregistrationstudentinformation` `schl_enr_reg_stud_info`
                            ON `schl_enr_reg`.`SchlEnrollRegSms_ID` = `schl_enr_reg_stud_info`.`SchlEnrollReg_ID`
                          LEFT JOIN `schoolenrollmentassessment` `schl_enr_as`
                            ON `schl_enr_reg`.`SchlStud_ID` = `schl_enr_as`.`SchlStud_ID`
                          WHERE `schl_stud`.`SchlStudSms_ID` = $USERID
                            AND `schl_enr_as`.`SchlAcadLvl_ID` = $LVLID
                            AND `schl_enr_as`.`SchlAcadYr_ID` = $YRID
                            AND `schl_enr_as`.`SchlAcadPrd_ID` = $PRDID
                            AND `schl_enr_reg`.`SchlAcadLvl_ID` = $LVLID";
                            
    $rreg = $dbConn->query($qry_get_subj_list);
    $stud_subj_list = $rreg->fetch_assoc();          
    $subj_list = $stud_subj_list['schl_acad_subj_id'];

    $qry = "SELECT `schl_enr_subj_off`.`SchlEnrollSubjOffSms_ID` AS `subj_id`,
                   `schl_acad_subj`.`SchlAcadSubj_CODE` AS `subj_code`,
                   `schl_acad_subj`.`SchlAcadSubj_desc` AS `subj_desc`,
                   `schl_enr_subj_off`.`SchlEnrollSubjOff_UNIT` AS `schl_subj_unit`,
                   `schl_enr_subj_off`.`SchlProf_ID` AS `prof_id`,
                   `schl_enr_subj_off`.`SchlAcadYr_ID` AS `acad_year_id`,
                   `schl_enr_subj_off`.`SchlAcadLvl_ID` AS `acad_lvl_id`,
                   `schl_enr_subj_off`.`SchlAcadPrd_ID` AS `acad_prd_id`,
                   (
                       SELECT REPLACE(GROUP_CONCAT(`schl_emp`.`SchlEmp_FNAME`, ' ', `schl_emp`.`SchlEmp_LNAME`), ',', ' / ')
                       FROM `schoolemployee` `schl_emp`
                       WHERE FIND_IN_SET(`schl_emp`.`SchlEmpSms_ID`, `schl_enr_subj_off`.`SchlProf_ID`)
                   ) AS `prof_name`,
            COUNT(studrec.schltadi_id) AS record_count_today
            FROM schoolenrollmentsubjectoffered schl_enr_subj_off
            LEFT JOIN schoolacademicsubject schl_acad_subj
                ON schl_enr_subj_off.SchlAcadSubj_ID = schl_acad_subj.SchlAcadSubjSms_ID
            LEFT JOIN schooltadi studrec
                ON schl_enr_subj_off.SchlEnrollSubjOffSms_ID = studrec.schlenrollsubjoff_id
                AND DATE(studrec.schltadi_date) = CURDATE()
				-- AND studrec.`schlstud_id` = $USERID
            WHERE 
				schl_enr_subj_off.SchlEnrollSubjOffSms_ID IN ($subj_list)
			
            GROUP BY schl_enr_subj_off.SchlEnrollSubjOffSms_ID";
    
    $rreg = $dbConn->query($qry);
    $fetch = $rreg->fetch_all(MYSQLI_ASSOC);

    foreach ($fetch as &$row) {
        $row['user_id'] = $USERID;
    }
    unset($row);
}

if($type === 'GET_SUBMITTED_REC'){

    $subj_Id = $_POST['subj_Id'];
    $prof_Id = $_POST['prof_Id'];
    // $USERID = $_SESSION['STUDENT']['USERID'];

    $qry = "SELECT 
				schl_tadi.`schltadi_id` AS schltadi_ID,
				schl_tadi.`schltadi_date` AS tadi_date,
				CONCAT(schl_tadi.`schltadi_mode`, ' ', schl_tadi.`schltadi_type`) AS tadi_modeType,
				schl_tadi.`schltadi_timein` AS tadi_timeIn,
				schl_tadi.`schltadi_timeout` AS tadi_timeOut,
				schl_tadi.`schltadi_activity` AS tadi_act,
				schl_tadi.`schltadi_status` AS tadi_status,
				schl_tadi.`schltadi_filepath` AS tadi_filepath,
				schl_tadi.schlenrollsubjoff_id AS sub_off_id,
				schl_tadi.SchlProf_ID 
			FROM `schooltadi` AS schl_tadi 
			
			LEFT JOIN `schoolstudent` AS schl_stud 
				ON schl_tadi.`schlstud_id` = schl_stud.`SchlStudSms_ID` 

			LEFT JOIN `schoolenrollmentregistration` AS schl_enr_reg 
				ON schl_stud.`SchlEnrollRegColl_ID` = schl_enr_reg.`SchlEnrollRegSms_ID` 

			LEFT JOIN `schoolenrollmentregistrationstudentinformation` AS schl_reg_stud 
				ON schl_enr_reg.`SchlEnrollRegSms_ID` = `schl_reg_stud`.`SchlEnrollReg_ID` 

			WHERE `schlprof_id` =  ?
			AND `schlenrollsubjoff_id` =  ?
			-- AND `schl_tadi`.`schlstud_id` = ?
            AND `schltadi_date` = CURDATE()
			ORDER BY schl_tadi.`schltadi_date`, schl_tadi.`schltadi_timein`";
    
    $stmt = $dbConn->prepare($qry);
	$stmt->bind_param("ii",$prof_Id,$subj_Id);
	$stmt->execute();
	$result = $stmt->get_result();
	$fetch = $result->fetch_all(MYSQLI_ASSOC);
	$stmt->close();
}

if($type == 'GET_IMAGE'){

	$prof_id = $_POST['prof_id'];
	$REC_ID = $_POST['tadi_id'];

	$qry = "SELECT 
				`schltadi_filepath` AS `tadi_filepath`,
				`tadi_exifDate` AS exif_date,
				`tadi_exifTime` AS exif_time,
				`schltadi_date` AS upld_date,
				`schltadi_timein` AS upld_time
			FROM `schooltadi`
			WHERE `schlprof_id` = ?
			AND `schltadi_id` = ?";
	
	$stmt = $dbConn->prepare($qry);
	$stmt->bind_param("ii", $prof_id, $REC_ID);
	$stmt->execute();
	$result = $stmt->get_result();
	$fetch = $result->fetch_assoc();
	$stmt->close();
}

$dbConn->close();
echo json_encode($fetch);
