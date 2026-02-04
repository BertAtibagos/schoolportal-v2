<?php 
// ✅ Secure cookie flags (must be set before session_start)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');

// PHP error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
include('../../../../configuration/connection-config.php');

$fetch = "";

$type = $_POST['type'] ?? '';

if ($type === 'GET_SCHOOL_YEAR') {

    $qry = "SELECT `SchlAcadYr_NAME` AS `AcadYr_Name`,
                   `SchlAcadYr_DESC` AS `AcadYr_Desc`,
                   `SchlAcadYrSms_ID` AS `AcadYr_ID`
            FROM `schoolacademicyear`
            WHERE `SchlAcadYr_ID` = " . $_SESSION['YRID'] . "
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
            WHERE `SchlAcadPrdSms_ID` = " . $_SESSION['PRDID'] . "
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
            WHERE `SchlAcadYrLvlSms_ID` = " . $_SESSION['YRLVLID'] . " 
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
            WHERE `SchlAcadLvlSms_ID` =  " . $_SESSION['LVLID'] . " 
            AND `SchlAcadLvl_STATUS` = 1 
            AND `SchlAcadLvl_ISACTIVE` = 1";

    $rreg = $dbConn->query($qry);
    $fetch = $rreg->fetch_all(MYSQLI_ASSOC);
}

if ($type === 'GET_SUBJECT_LIST') {

    $USERID = $_SESSION['USERID'];
    $LVLID  = $_SESSION['LVLID'];
    $YRID   = $_SESSION['YRID']; 
    $PRDID  = $_SESSION['PRDID']; 

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
                   ) AS `prof_name`
            FROM `schoolenrollmentsubjectoffered` `schl_enr_subj_off`
            LEFT JOIN `schoolacademicsubject` `schl_acad_subj`
                ON `schl_enr_subj_off`.`SchlAcadSubj_ID` = `schl_acad_subj`.`SchlAcadSubjSms_ID`
            WHERE `SchlEnrollSubjOffSms_ID` IN ($subj_list)";
    
    $rreg = $dbConn->query($qry);
    $fetch = $rreg->fetch_all(MYSQLI_ASSOC);
}

$dbConn->close();
echo json_encode($fetch);
