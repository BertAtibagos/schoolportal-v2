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

    $type = $_POST['type'];

    if ($type == 'GET_SUBJECT_LIST') {
        $lvlid = $_POST['lvl_id'];
        $prdid = $_POST['prd_id'];
        $yrid = $_POST['yr_id'];
        $yrlvlid = $_POST['yrlvl_id'];
        $USERID = $_SESSION['EMPLOYEE']['ID']; 
        $search = $_POST['search'];  

        $qry = "SELECT DISTINCT
                    `schl_acad_sec`.`SchlAcadSec_NAME` AS schl_sec,
                    `schl_acad_subj`.`SchlAcadSubj_CODE` AS `subj_code`,
                    `schl_acad_subj`.`SchlAcadSubj_desc` AS `subj_desc`,
                    `schl_enr_subj_off`.`SchlProf_ID` AS `prof_id`,
                    `schl_enr_subj_off`.`SchlEnrollSubjOff_ISACTIVE` AS `subj_act`,
                    schl_enr_subj_off.`SchlEnrollSubjOffSms_ID` AS sub_off_id,
                    (
                    SELECT 
                        COUNT(*) 
                    FROM
                        `schooltadi` AS t 
                        WHERE t.`schlprof_id` = `schl_enr_subj_off`.`SchlProf_ID` 
                        AND t.`schlenrollsubjoff_id` = `schl_enr_subj_off`.`SchlEnrollSubjOffSms_ID`) AS total_count,
                    (
                        SELECT COUNT(*) 
                        FROM `schooltadi` AS t
                        WHERE t.`schltadi_status` = 0
                        AND t.`schlprof_id` = `schl_enr_subj_off`.`SchlProf_ID`
                        AND t.`schlenrollsubjoff_id` = `schl_enr_subj_off`.`SchlEnrollSubjOffSms_ID`
                    ) AS unverified_count
                FROM
                    `schoolenrollmentsubjectoffered` AS `schl_enr_subj_off`
                LEFT JOIN
                    `schoolacademicsubject` AS `schl_acad_subj` 
                    ON `schl_enr_subj_off`.`SchlAcadSubj_ID` = `schl_acad_subj`.`SchlAcadSubjSms_ID`
                LEFT JOIN
                    `schoolacademicsection` AS `schl_acad_sec` 
                    ON `schl_enr_subj_off`.`SchlAcadSec_ID` = `schl_acad_sec`.`SchlAcadSecSms_ID`
                LEFT JOIN 
                    `schoolacademiccourses` AS `schl_acad_crses` 
                    ON `schl_enr_subj_off`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID`

                LEFT JOIN 
                    `schooldepartment` AS `schl_dept` 
                    ON `schl_acad_crses`.`SchlDept_ID` = `schl_dept`.`SchlDeptSms_ID`
                LEFT JOIN
                    `schoolacademicyearperiod` AS `schl_acad_yr_prd` 
                    ON `schl_enr_subj_off`.`SchlAcadYr_ID` = `schl_acad_yr_prd`.`SchlAcadYr_ID`
                    
                LEFT JOIN
                    `schoolacademicyear` AS `schl_yr` ON `schl_acad_yr_prd`.`SchlAcadYr_ID` = `schl_yr`.`SchlAcadYrSms_ID`
                WHERE`schl_enr_subj_off`.`SchlAcadLvl_ID` = ?
                AND `schl_acad_yr_prd`.`SchlAcadYr_ID` = ?
                AND `schl_enr_subj_off`.`SchlAcadPrd_ID` = ?
                AND `schl_enr_subj_off`.`SchlProf_ID` = ?
                AND schl_enr_subj_off.`SchlAcadYrLvl_ID`= ?
                AND `schl_enr_subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1 
                AND `schl_acad_subj`.`SchlAcadSubj_CODE` LIKE ? ";

             $stmt = $dbConn->prepare($qry);

        if ($stmt) {
            
            $searchTerm = "%" . $search . "%";
            $stmt->bind_param("iiiiis", $lvlid, $yrid, $prdid, $USERID, $yrlvlid, $searchTerm);

            $stmt->execute();
            $result = $stmt->get_result();
            $fetch = $result->fetch_all(MYSQLI_ASSOC);

            $stmt->close();
            $dbConn->close();
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to prepare SQL statement."]);
        }
    }

    if($type == 'CHECK_MATCHED_SUBJ_ID'){

        $sub_off_id = $_POST['sub_off_id'];

        $qry = "SELECT 
                    `schl_tadi`.`schltadi_id` `tadi_id`,
                    `schl_acad_subj`.`SchlAcadSubj_CODE` AS `subj_code`,
                    `schl_acad_subj`.`SchlAcadSubj_NAME` AS `subj_name`,
                    `schl_acad_subj`.`SchlAcadSubj_DESC` AS `subj_desc`,
                    CONCAT ( 
                        `schl_emp`.`SchlEmp_FNAME`, ' ',
                        `schl_emp`.`SchlEmp_LNAME`) AS `prof_name`,
                    `schl_tadi`.`schltadi_date` AS `schltadi_date`,
                    `schl_tadi`.`schltadi_mode` AS `tadi_mode`,
                    `schl_tadi`.`schltadi_type` AS `tadi_type`,
                    `schl_tadi`.`schltadi_timein` AS `time_in`,
                    `schl_tadi`.`schltadi_timeout` AS `time_out`,
                    `schl_tadi`.`schltadi_activity` AS `tadi_activity`,
                    `schl_tadi`.`schltadi_isactive` AS `is_active`,
                    `schl_tadi`.`schltadi_status` AS `tadi_status`,
                    `schl_tadi`.`schltadi_isconfirm` AS `is_confirm`,
                    `schl_tadi`.`schlstud_id` AS `stud_id`,
                    `schl_tadi`.`schlacadlvl_id` AS `acad_lvl_id`,
                    `schl_tadi`.`schlacadyr_id` AS `acad_yr_id`,
                    `schl_tadi`.`schlenrollsubjoff_id` AS `sub_off_id`,
                    `schl_tadi`.`schlprof_id` AS `prof_id`,
                    CONCAT(
                    `SchlEnrollRegStudInfo_FIRST_NAME`,' ',
                    `SchlEnrollRegStudInfo_MIDDLE_NAME`,' ',
                    `SchlEnrollRegStudInfo_LAST_NAME`) AS STUD_NAME
                FROM 	`schooltadi` `schl_tadi`
                
                LEFT JOIN `schoolstudent` AS schl_stud 
                    ON schl_tadi.`schlstud_id` = schl_stud.`SchlStudSms_ID`
                            
                LEFT JOIN `schoolenrollmentregistration` AS schl_enr_reg 
                    ON schl_stud.`SchlEnrollRegColl_ID` = schl_enr_reg .`SchlEnrollRegSms_ID`
                    
                LEFT JOIN`schoolenrollmentregistrationstudentinformation` AS schl_reg_stud 
                    ON schl_enr_reg .`SchlEnrollRegSms_ID` = `schl_reg_stud`.`SchlEnrollReg_ID`
                
                LEFT JOIN `schoolenrollmentsubjectoffered` AS `schl_enr_subj_off`
                    ON `schl_tadi`.`schlenrollsubjoff_id` = `schl_enr_subj_off`.`SchlEnrollSubjOffSms_ID`

                LEFT JOIN `schoolacademicsubject` AS `schl_acad_subj`
                    ON `schl_enr_subj_off`.`SchlAcadSubj_ID` =  `schl_acad_subj`.`SchlAcadSubjSms_ID`
                
                LEFT JOIN `schoolemployee` AS `schl_emp`
                    ON `schl_tadi`.`schlprof_id` = `schl_emp`.`SchlEmpSms_ID`
                            
                WHERE `SchlEnrollSubjOffSms_ID` = ? 
                AND `schltadi_isconfirm` = 1
                ORDER BY `schl_tadi`.`schltadi_date` DESC";

        $stmt = $dbConn->prepare($qry);
        $stmt->bind_param("i", $sub_off_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $dbConn->close();
    }

    if($type == 'SEARCH_TADI_DATA_BY_DATE'){

        $search_date = $_POST['search_date'];

        $qry = "SELECT 
                    `schl_tadi`.`schltadi_id` AS `tadi_id`,
                    `schl_acad_subj`.`SchlAcadSubj_CODE` AS `subj_code`,
                    `schl_acad_subj`.`SchlAcadSubj_NAME` AS `subj_name`,
                    `schl_acad_subj`.`SchlAcadSubj_DESC` AS `subj_desc`,
                    CONCAT(`schl_emp`.`SchlEmp_FNAME`, ' ', `schl_emp`.`SchlEmp_LNAME`) AS `prof_name`,
                    `schl_tadi`.`schltadi_date` AS `schltadi_date`,
                    `schl_tadi`.`schltadi_mode` AS `tadi_mode`,
                    `schl_tadi`.`schltadi_type` AS `tadi_type`,
                    `schl_tadi`.`schltadi_timein` AS `schltadi_timein`,
                    `schl_tadi`.`schltadi_timeout` AS `schltadi_timeout`,
                    `schl_tadi`.`schltadi_activity` AS `tadi_activity`,
                    CONCAT(`SchlEnrollRegStudInfo_FIRST_NAME`, ' ', `SchlEnrollRegStudInfo_MIDDLE_NAME`, ' ', `SchlEnrollRegStudInfo_LAST_NAME`) AS `STUD_NAME`
                FROM `schooltadi` AS `schl_tadi`
                LEFT JOIN `schoolstudent` AS `schl_stud` 
                    ON `schl_tadi`.`schlstud_id` = `schl_stud`.`SchlStudSms_ID`
                LEFT JOIN `schoolenrollmentregistration` AS `schl_enr_reg` 
                    ON `schl_stud`.`SchlEnrollRegColl_ID` = `schl_enr_reg`.`SchlEnrollRegSms_ID`
                LEFT JOIN `schoolenrollmentregistrationstudentinformation` AS `schl_reg_stud` 
                    ON `schl_enr_reg`.`SchlEnrollRegSms_ID` = `schl_reg_stud`.`SchlEnrollReg_ID`
                LEFT JOIN `schoolenrollmentsubjectoffered` AS `schl_enr_subj_off` 
                    ON `schl_tadi`.`schlenrollsubjoff_id` = `schl_enr_subj_off`.`SchlEnrollSubjOffSms_ID`
                LEFT JOIN `schoolacademicsubject` AS `schl_acad_subj` 
                    ON `schl_enr_subj_off`.`SchlAcadSubj_ID` = `schl_acad_subj`.`SchlAcadSubjSms_ID`
                LEFT JOIN `schoolemployee` AS `schl_emp` 
                    ON `schl_tadi`.`schlprof_id` = `schl_emp`.`SchlEmpSms_ID`

                WHERE `schl_tadi`.`schltadi_date` = ? 
                AND `schl_tadi`.`schltadi_isconfirm` = 1
                ORDER BY `schl_tadi`.`schltadi_date` DESC ";

        $stmt = $dbConn->prepare($qry);
        $stmt->bind_param("i", $search_date);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $dbConn->close();
    }

    if($type == 'GET_ACADEMIC_LEVEL'){
        $user = $_SESSION['EMPLOYEE']['ID'];

        $qry = "SELECT DISTINCT
                    acad_lvl.`SchlAcadLvl_ID` AcadLvl_ID,
                    acad_lvl.`SchlAcadLvl_NAME` AcadLvl_Name,
                    acad_lvl.`SchlAcadLvl_DESC` 
                FROM
                    `schoolacademiclevel` acad_lvl 
                LEFT JOIN `schoolenrollmentsubjectoffered` subj_off 
                    ON acad_lvl.`SchlAcadLvlSms_ID` = subj_off.`SchlAcadLvl_ID` 
                LEFT JOIN `schooldepartment` `schl_dept` 
                    ON acad_lvl.`SchlAcadLvlSms_ID` = `schl_dept`.`SchlAcadLvl_ID`
                WHERE `SchlAcadLvl_ISACTIVE` = 1
                AND  `subj_off`.`SchlProf_ID` = ?
                ORDER BY AcadLvl_Name DESC";

        $stmt = $dbConn->prepare($qry);
        $stmt->bind_param("i",$user);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $dbConn->close();

    }

    if($type == 'GET_ACADEMIC_YEAR_LEVEL') {

	    $lvlid = $_POST['lvl_id'];

	    $qry = "SELECT 
                    `SchlAcadYrLvlSms_ID` AS `ACAD_YRLVL_ID`,
					`SchlAcadYrLvl_NAME` AS `ACAD_YRLVL_NAME`
				FROM `schoolacademicyearlevel`
				WHERE `SchlAcadYrLvl_STATUS` = 1 
                AND `SchlAcadYrLvl_ISACTIVE` = 1 
                AND `SchlAcadLvl_ID` = ? ORDER BY `SchlAcadYrLvl_RANKNO` ";

        $stmt = $dbConn->prepare($qry);
        $stmt->bind_param("i", $lvlid);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $dbConn->close();
    }

    if ($type == 'GET_ACADEMIC_PERIOD') {
        $lvlid = $_POST['lvl_id'];

        $qry = "SELECT DISTINCT 
                    `schl_acad_prd`.`SchlAcadPrdSms_ID` AS `acad_prd_id`,
                    `schl_acad_prd`.`SchlAcadPrd_NAME` AS `acad_prd_name`
                FROM `schoolacademicyearperiod` AS `schl_acad_yr_prd`
                LEFT JOIN `schoolacademicperiod` AS `schl_acad_prd`
                    ON `schl_acad_yr_prd`.`SchlAcadPrd_ID` =  `schl_acad_prd`.`SchlAcadPrdSms_ID`
                WHERE `schl_acad_yr_prd`.`SchlAcadLvl_ID` = ? 
                AND `schl_acad_yr_prd`.`SchlAcadYrPrd_ISACTIVE` = 1 ";

        $stmt = $dbConn->prepare($qry);
        $stmt->bind_param("i", $lvlid);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $dbConn->close();
    }

    if ($type == 'GET_ACAD_YEAR') {

        $lvlid = $_POST['lvl_id'];
        $prdid = $_POST['prd_id'];

        $qry = "  SELECT  
                    `schl_acad_yr_prd`.`SchlAcadLvl_ID` AS `YEAR_ID`,
                    `schl_yr`.`SchlAcadYr_DESC` AS `YEAR_NAME`,
                    schl_yr.`SchlAcadYrSms_ID` AS `Period_id`
                FROM `schoolacademicyearperiod` AS `schl_acad_yr_prd`  
                LEFT JOIN `schoolacademicyear` AS `schl_yr`  
                    ON `schl_acad_yr_prd`.`SchlAcadYr_ID` = `schl_yr`.`SchlAcadYrSms_ID`
                WHERE `schl_acad_yr_prd`.`SchlAcadYrPrd_ISACTIVE` = 1 
                AND `schl_acad_yr_prd`.`SchlAcadLvl_ID` = ? 
                AND `schl_acad_yr_prd`.`SchlAcadPrd_ID` = ?
                ORDER BY  `schl_yr`.`SchlAcadYr_DESC` DESC";
                
        $stmt = $dbConn->prepare($qry);
        $stmt->bind_param("ii", $lvlid, $prdid);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $dbConn->close();
    }

    if ($type == 'GET_INSTRUCTOR_DETAILS') {

        $USERID = $_SESSION['EMPLOYEE']['ID'];
        $qry = "SELECT DISTINCT
                    tadi.schltadi_id,
                    acad_subj.SchlAcadSubj_CODE AS subj_code,
                    acad_subj.SchlAcadSubj_NAME AS subj_name,
                    acad_subj.SchlAcadSubj_DESC AS subj_desc,
                    CONCAT(emp.SchlEmp_LNAME, ',', emp.SchlEmp_FNAME, ' ', emp.SchlEmp_MNAME) AS prof_name,
                    tadi.schltadi_date,
                    tadi.schltadi_mode,
                    tadi.schltadi_type,
                    tadi.schltadi_timein,
                    tadi.schltadi_timeout,
                    tadi.schlenrollsubjoff_id AS sub_off_id,
                    tadi.schlprof_id
                FROM schooltadi AS tadi
                LEFT JOIN schoolenrollmentsubjectoffered AS enr_subj_off 
                    ON tadi.schlenrollsubjoff_id = enr_subj_off.SchlEnrollSubjOffSms_ID
                LEFT JOIN schoolacademicsubject AS acad_subj 
                    ON enr_subj_off.SchlAcadSubj_ID = acad_subj.SchlAcadSubjSms_ID
                LEFT JOIN schoolemployee AS emp 
                    ON tadi.schlprof_id = emp.SchlEmpSms_ID
                WHERE emp.`SchlEmp_ID` = ? ";

        $stmt = $dbConn->prepare($qry);
        $stmt->bind_param("i", $USERID);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch = $result->fetch_all(MYSQLI_ASSOC);  
        $stmt->close();
        $dbConn->close();
    }

    if($type == 'GET_TADI_RECORD'){

        $USERID = $_SESSION['EMPLOYEE']['ID'];
        $strtDateSearch = $_POST['strtDateSearch'];
        $endDateSearch = $_POST['endDateSearch'];
        $subj_off_id = $_POST['subj_off_id'];

        $date_str = "";

        $qry = "SELECT 
                    CONCAT(
                        `SchlEnrollRegStudInfo_LAST_NAME`,
                        ', ',
                        `SchlEnrollRegStudInfo_FIRST_NAME`,
                        ' ',
                        `SchlEnrollRegStudInfo_MIDDLE_NAME`
                    ) AS stud_name,
                    schl_tadi.`schltadi_id` AS schltadi_ID,
                    schl_tadi.`schltadi_date` AS tadi_date,
                    schl_tadi.`schltadi_mode` AS tadi_mode,
                    schl_tadi.`schltadi_type` AS tadi_type,
                    schl_tadi.`schltadi_timein` AS tadi_timein,
                    schl_tadi.`schltadi_timeout` AS tadi_timeout,
                    schl_tadi.`schltadi_activity` AS tadi_act,
                    schl_tadi.`schltadi_status` AS tadi_status,
                    schl_tadi.`schltadi_filepath` AS tadi_filepath,
                    schl_tadi.schlenrollsubjoff_id AS sub_off_id,
                    schl_tadi.schltadi_late_status AS late_status,
                    schl_tadi.schltadi_mkup_date AS mkup_date,
                    schl_tadi.schltadi_isconfirm AS approve

                    FROM `schooltadi` AS schl_tadi
                    LEFT JOIN `schoolstudent` AS schl_stud 
                        ON schl_tadi.`schlstud_id` = schl_stud.`SchlStudSms_ID` 
                    LEFT JOIN `schoolenrollmentregistration` AS schl_enr_reg 
                        ON schl_stud.`SchlEnrollRegColl_ID` = schl_enr_reg.`SchlEnrollRegSms_ID` 
                    LEFT JOIN `schoolenrollmentregistrationstudentinformation` AS schl_reg_stud 
                        ON schl_enr_reg.`SchlEnrollRegSms_ID` = `schl_reg_stud`.`SchlEnrollReg_ID`
                    WHERE `schlprof_id` = ?
                    AND  `schlenrollsubjoff_id`= ?
                    AND `schl_tadi`.`schltadi_date` BETWEEN ? AND ?
                    ORDER BY schl_tadi.`schltadi_date` DESC";

        $stmt = $dbConn->prepare($qry);
        $stmt->bind_param("iiss", $USERID, $subj_off_id, $strtDateSearch, $endDateSearch);
        $stmt->execute();
        $rreg = $stmt->get_result();
        $fetch = $rreg->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $dbConn->close();
    }

    if($type == 'GETALL_TADI_RECORD'){

        $USERID = $_SESSION['EMPLOYEE']['ID'];
        $subj_off_id = $_POST['subj_off_id'];
        $qry = "SELECT 
                    CONCAT(
                        `SchlEnrollRegStudInfo_LAST_NAME`,
                        ', ',
                        `SchlEnrollRegStudInfo_FIRST_NAME`,
                        ' ',
                        `SchlEnrollRegStudInfo_MIDDLE_NAME`
                    ) AS stud_name,
                    schl_tadi.`schltadi_id` AS schltadi_ID,
                    schl_tadi.`schltadi_date` AS tadi_date,
                    schl_tadi.`schltadi_mode` AS tadi_mode,
                    schl_tadi.`schltadi_type` AS tadi_type,
                    schl_tadi.`schltadi_timein` AS tadi_timein,
                    schl_tadi.`schltadi_timeout` AS tadi_timeout,
                    schl_tadi.`schltadi_activity` AS tadi_act,
                    schl_tadi.`schltadi_status` AS tadi_status,
                    schl_tadi.`schltadi_filepath` AS tadi_filepath,
                    schl_tadi.schlenrollsubjoff_id AS sub_off_id,
                    schl_tadi.schltadi_late_status AS late_status,
                    schl_tadi.schltadi_mkup_date AS mkup_date,
                    schl_tadi.schltadi_isconfirm AS approve

                    FROM `schooltadi` AS schl_tadi
                    LEFT JOIN `schoolstudent` AS schl_stud 
                        ON schl_tadi.`schlstud_id` = schl_stud.`SchlStudSms_ID` 
                    LEFT JOIN `schoolenrollmentregistration` AS schl_enr_reg 
                        ON schl_stud.`SchlEnrollRegColl_ID` = schl_enr_reg.`SchlEnrollRegSms_ID` 
                    LEFT JOIN `schoolenrollmentregistrationstudentinformation` AS schl_reg_stud 
                        ON schl_enr_reg.`SchlEnrollRegSms_ID` = `schl_reg_stud`.`SchlEnrollReg_ID`
                    WHERE `schlprof_id` = ?
                    AND  `schlenrollsubjoff_id`= ?
                    ORDER BY schl_tadi.`schltadi_date` DESC, schl_tadi.`schltadi_timein` DESC";

        $stmt = $dbConn->prepare($qry);
        $stmt->bind_param("ii", $USERID, $subj_off_id);
        $stmt->execute();
        $rreg = $stmt->get_result();
        $fetch = $rreg->fetch_all(MYSQLI_ASSOC);
        
    }

    if($type == 'GET_IMAGE'){

        $USERID = $_SESSION['EMPLOYEE']['ID'];
        $REC_ID = $_POST['tadi_id'];
        $qry = "SELECT 
                    `schltadi_filepath` AS `tadi_filepath`,
                    `tadi_exifDate` AS exif_date,
                    `tadi_exifTime` AS exif_time,
                    `schltadi_date` AS upld_date,
                    `schltadi_timein` AS upld_time
                FROM 
                    `schooltadi`
                WHERE 
                    `schlprof_id` = ?
                AND 
                    `schltadi_id` = ?";
        
        $stmt = $dbConn->prepare($qry);
        $stmt->bind_param("ii", $USERID, $REC_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch = $result->fetch_assoc();
        $stmt->close();
        $dbConn->close();
    }

    if($type == 'GET_ALL_TADI_SUMMARY'){

        $lvlid = $_POST['lvl_id'];
        $prdid = $_POST['prd_id'];
        $yrid = $_POST['yr_id'];
        $USERID = $_SESSION['EMPLOYEE']['ID']; 

        $qry = "SELECT DISTINCT 
                    `schl_acad_sec`.`SchlAcadSec_NAME` AS schl_sec,
                    `schl_acad_subj`.`SchlAcadSubj_CODE` AS `subj_code`,
                    `schl_acad_subj`.`SchlAcadSubj_desc` AS `subj_desc`,
                    `schl_enr_subj_off`.`SchlProf_ID` AS `prof_id`,
                    `schl_enr_subj_off`.`SchlEnrollSubjOff_ISACTIVE` AS `subj_act`,
                    schl_enr_subj_off.`SchlEnrollSubjOffSms_ID` AS sub_off_id,
                (SELECT 
                    COUNT(*) 
                FROM
                    `schooltadi` AS t 
                WHERE t.`schlprof_id` = `schl_enr_subj_off`.`SchlProf_ID` 
                    AND t.`schlenrollsubjoff_id` = `schl_enr_subj_off`.`SchlEnrollSubjOffSms_ID`) AS total_count,
                (SELECT 
                    COUNT(*) 
                FROM
                    `schooltadi` AS t 
                WHERE t.`schltadi_status` = 0 
                    AND t.`schlprof_id` = `schl_enr_subj_off`.`SchlProf_ID` 
                    AND t.`schlenrollsubjoff_id` = `schl_enr_subj_off`.`SchlEnrollSubjOffSms_ID`) AS unverified_count 
                FROM
                `schoolenrollmentsubjectoffered` AS `schl_enr_subj_off` 
                LEFT JOIN `schoolacademicsubject` AS `schl_acad_subj` 
                    ON `schl_enr_subj_off`.`SchlAcadSubj_ID` = `schl_acad_subj`.`SchlAcadSubjSms_ID` 
                LEFT JOIN `schoolacademicsection` AS `schl_acad_sec` 
                    ON `schl_enr_subj_off`.`SchlAcadSec_ID` = `schl_acad_sec`.`SchlAcadSecSms_ID` 
                LEFT JOIN `schoolacademiccourses` AS `schl_acad_crses` 
                    ON `schl_enr_subj_off`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID` 
                LEFT JOIN `schooldepartment` AS `schl_dept` 
                    ON `schl_acad_crses`.`SchlDept_ID` = `schl_dept`.`SchlDeptSms_ID` 
                LEFT JOIN `schoolacademicyearperiod` AS `schl_acad_yr_prd` 
                    ON `schl_enr_subj_off`.`SchlAcadYr_ID` = `schl_acad_yr_prd`.`SchlAcadYr_ID` 
                LEFT JOIN `schoolacademicyear` AS `schl_yr` 
                    ON `schl_acad_yr_prd`.`SchlAcadYr_ID` = `schl_yr`.`SchlAcadYrSms_ID` 
                WHERE `schl_enr_subj_off`.`SchlAcadLvl_ID` = ? 
                AND `schl_acad_yr_prd`.`SchlAcadYr_ID` = ?
                AND `schl_enr_subj_off`.`SchlAcadPrd_ID` = ? 
                AND `schl_enr_subj_off`.`SchlProf_ID` = ?
                AND `schl_enr_subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1 
                ORDER BY unverified_count DESC, total_count DESC ";


        $stmt = $dbConn->prepare($qry);
        $stmt->bind_param("iiii", $lvlid, $yrid, $prdid, $USERID);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $dbConn->close();
    }

    if($type == 'GET_TOTAL_COUNT_SUMMARY'){

        $user = $_SESSION['EMPLOYEE']['ID'];

        $qry = "WITH counts AS (
                    SELECT 
                        SUM(CASE WHEN schltadi_status = 1 THEN 1 ELSE 0 END) AS verified_count,
                        SUM(CASE WHEN schltadi_status = 0 THEN 1 ELSE 0 END) AS total_unverified,
                        COUNT(*) AS total_count
                    FROM schooltadi 
                    WHERE schlprof_id = ?
                )
                SELECT 
                    verified_count,
                    total_unverified,
                    total_count,
                    ROUND((verified_count / total_count) * 100) AS verification_rate
                FROM counts";

        $stmt = $dbConn->prepare($qry);
        $stmt->bind_param("i",$user);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch = $result->fetch_assoc();
        $stmt->close();
        $dbConn->close();
    }

    if($type == 'UPDATE_SUBJECT_COUNT'){

        $subj_off = $_POST['sub_off_id'];

        $qry = "WITH counts AS 
                (SELECT 
                SUM(
                    CASE
                    WHEN schltadi_status = 1 
                    THEN 1 
                    ELSE 0 
                    END
                ) AS verified_count,
                SUM(
                    CASE
                    WHEN schltadi_status = 0 
                    THEN 1 
                    ELSE 0 
                    END
                ) AS total_unverified,
                COUNT(*) AS total_count 
                FROM
                schooltadi 
                WHERE `schlenrollsubjoff_id` = ?) 
                SELECT 
                verified_count,
                total_unverified,
                total_count
                FROM
                counts ";

        $stmt = $dbConn->prepare($qry);
        $stmt->bind_param("i",$subj_off);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch = $result->fetch_assoc();
        $stmt->close();
        $dbConn->close();
    }

    echo json_encode($fetch);

?>




