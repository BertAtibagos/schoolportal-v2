<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');

// PHP error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
include('../../../../../configuration/connection-config.php');

function get_current_academic_period_id($dbConn, $acadLvlId, $acadYrId) {
        $qry = "SELECT `SchlAcadPrd_ID` AS acad_prd_id
                        FROM `schoolacademicyearperiod`
                        WHERE `SchlAcadLvl_ID` = ?
                            AND `SchlAcadYr_ID` = ?
                            AND `SchlAcadYrPrd_ISACTIVE` = 1
                            AND `SchlAcadYrPrd_ISOPEN` = 1
                        ORDER BY `SchlAcadYrPrdSms_ID` DESC
                        LIMIT 1";

        $stmt = $dbConn->prepare($qry);
        $stmt->bind_param('ii', $acadLvlId, $acadYrId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if (!$row || !isset($row['acad_prd_id'])) {
                return null;
        }

        $periodId = intval($row['acad_prd_id']);
        return $periodId > 0 ? $periodId : null;
}

$type = $_POST['type'];
$queryType = ['GET_ALL_TOTAL', 'GET_TOTAL_PER_MONTH', 'GET_TOTAL_PER_CUTOFF', 'GET_ALL_PROG_TOTAL', 'GET_TADI_DETAILS_BY_CUTOFF', 'GET_INSTRUCTOR_LIST_DEPT_SUMMARY', 'GET_ACADEMIC_LEVEL', 'GET_ACADEMIC_YEAR_LEVEL', 'GET_ACADEMIC_PERIOD','GET_ACAD_YEAR','GET_INSTRUCTOR_SCHEDULE','GET_TABULATION','RECORD_TABULATION'];
if($_SESSION['EMPLOYEE'] && in_array($type, $queryType, true)){
    $staticPeriod = 6;
    $staticYear = 19;
    switch($type){
        case 'GET_ALL_TOTAL':
            $qry="SELECT COUNT(*) total_rec,
                (SELECT COUNT(*)
                FROM `schooltadi`
                WHERE `schltadi_status` = 1
                    AND schltadi_isactive = 1) verified,
                (SELECT COUNT(*)
                FROM schooltadi
                WHERE `schltadi_status` = 0
                    AND schltadi_isactive = 1) unverified
                FROM schooltadi";

            $stmt = $dbConn->prepare($qry);
            $stmt->execute();
            $result = $stmt->get_result();
            $fetch = $result->fetch_assoc();
            $stmt->close();
            $dbConn->close();
            break;
        case 'GET_TOTAL_PER_MONTH':
            $qry = "SELECT 
                        MONTHNAME(`tadi_approved_date`) AS month_name,
                        SUM(schltadi_status = 1) AS verified,
                        SUM(schltadi_status = 0) AS unverified,
                        COUNT(*) AS total
                    FROM schooltadi
                    GROUP BY MONTH(`tadi_approved_date`)
                    ORDER BY MIN(`tadi_approved_date`)";
            
            $stmt = $dbConn->prepare($qry);
            $stmt->execute();
            $result = $stmt->get_result();
            $fetch = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            $dbConn->close();
            break;
        case 'GET_TOTAL_PER_CUTOFF':
            $qry = "SELECT 
                        CONCAT(MONTHNAME(`tadi_approved_date`), ' ', 
                            CASE 
                                WHEN DAY(`tadi_approved_date`) <= 15 THEN '1-15'
                                ELSE CONCAT('16-', DAY(LAST_DAY(`tadi_approved_date`)))
                            END) AS cutoff_period,
                        SUM(schltadi_status = 1) AS verified,
                        SUM(schltadi_status = 0) AS unverified,
                        COUNT(*) AS total
                    FROM schooltadi
                    GROUP BY YEAR(`tadi_approved_date`), MONTH(`tadi_approved_date`),
                            CASE 
                                WHEN DAY(`tadi_approved_date`) <= 15 THEN 1
                                ELSE 2
                            END
                    ORDER BY YEAR(`tadi_approved_date`), MONTH(`tadi_approved_date`),
                            CASE 
                                WHEN DAY(`tadi_approved_date`) <= 15 THEN 1
                                ELSE 2
                            END";
            
            $stmt = $dbConn->prepare($qry);
            $stmt->execute();
            $result = $stmt->get_result();
            $fetch = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            $dbConn->close();
            break;
        case 'GET_ALL_PROG_TOTAL':
            $qry = "SELECT
                    dept.`SchlDept_NAME` AS program_name,
                    COUNT(tadi.`schltadi_id`) AS total_records,
                    SUM(
                        CASE
                        WHEN tadi.`schltadi_status` = 1 
                        THEN 1 
                        ELSE 0 
                        END
                    ) AS verified_count,
                    SUM(
                        CASE
                        WHEN tadi.`schltadi_status` = 0 
                        THEN 1 
                        ELSE 0 
                        END
                    ) AS unverified_count,
                    ROUND(
                        (
                        SUM(
                            CASE
                            WHEN tadi.`schltadi_status` = 1 
                            THEN 1 
                            ELSE 0 
                            END
                        ) / COUNT(tadi.`schltadi_id`)
                        ) * 100,
                        2
                    ) AS verification_rate,
                    COUNT(DISTINCT emp.`SchlEmpSms_ID`) AS total_instructors,
                    COUNT(
                        DISTINCT off.`SchlEnrollSubjOffSms_ID`
                    ) AS total_subjects 
                    FROM
                    schooltadi tadi 
                    LEFT JOIN schoolstudent stud 
                        ON tadi.`schlstud_id` = stud.`SchlStudSms_ID` 
                    LEFT JOIN schoolenrollmentsubjectoffered off 
                        ON tadi.`schlenrollsubjoff_id` = off.`SchlEnrollSubjOffSms_ID` 
                    LEFT JOIN schoolacademiccourses crse 
                        ON off.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID` 
                    LEFT JOIN schooldepartment dept 
                        ON crse.`SchlDept_ID` = dept.`SchlDeptSms_ID` 
                    LEFT JOIN schoolemployee emp  
                        ON tadi.`schlprof_id` = emp.`SchlEmpSms_ID` 
                    WHERE off.`SchlAcadLvl_ID` = 2 
                        AND off.`SchlAcadYr_ID` = ?
                        AND off.`SchlAcadPrd_ID` = ?
                    GROUP BY dept.`SchlDept_NAME` 
                    ORDER BY unverified_count DESC ";

            $stmt = $dbConn->prepare($qry);
            $stmt->bind_param("ii", $staticYear, $staticPeriod);
            $stmt->execute();
            $result = $stmt->get_result();
            $fetch = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            $dbConn->close();
            break;
        case 'GET_TADI_DETAILS_BY_CUTOFF':

            if (!isset($_POST['rangeType'], $_POST['filterType'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
                exit;
			}

            $rangeType = $_POST['rangeType'];
            $filterType = $_POST['filterType'];

            $queryFilter = "";
            $binding = "";
            $values = [];
            $bind = '';

            if($rangeType == 'byDate'){
                if($filterType == 'deptName_all'){
                    $startDate = $_POST['startDate'];
                    $endDate = $_POST['endDate'];

                    $queryFilter = "WHERE tadi.tadi_approved_date BETWEEN ? AND ?";

                    $values = [$startDate, $endDate];
                    $bind = "ss";
                }else if($filterType == 'name_Search'){
                    $startDate = $_POST['startDate'];
                    $endDate = $_POST['endDate'];
                    $name = $_POST['name'];
                    $bindName = "%". $name . "%";

                    $queryFilter = "WHERE tadi.tadi_approved_date BETWEEN ? AND ?
                    AND CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`) LIKE ?";

                    $values = [$startDate, $endDate, $bindName];
                    $bind = "sss";
                }
                else if($filterType == 'dept_Search'){
                    $startDate = $_POST['startDate'];
                    $endDate = $_POST['endDate'];
                    $dept = $_POST['dept'];

                    $queryFilter = "WHERE tadi.tadi_approved_date BETWEEN ? AND ?
                    AND `dept`.`SchlDept_CODE` = ?";
                    
                    $values = [$startDate, $endDate, $dept];
                    $bind = "sss";
                }
            }

            // Calculate current and previous cut-off dates
            $today = date('Y-m-d');
            $current_day = date('d');
            $current_month = date('m');
            $current_year = date('Y');

            // Determine current cut-off period
            if ($current_day <= 15) {
                $current_cutoff_start = date('Y-m-01');
                $current_cutoff_end = date('Y-m-15');
                
                // Previous cut-off is 16-end of previous month
                $prev_month_start = new DateTime($current_year . '-' . $current_month . '-01');
                $prev_month_start->modify('-1 month');
                $prev_month_str = $prev_month_start->format('Y-m');
                $prev_cutoff_start = $prev_month_str . '-16';
                $prev_cutoff_end = $prev_month_str . '-' . $prev_month_start->format('t');
            } else {
                $current_cutoff_start = date('Y-m-16');
                $current_cutoff_end = date('Y-m-t');
                
                // Previous cut-off is 1-15 of current month
                $prev_cutoff_start = date('Y-m-01');
                $prev_cutoff_end = date('Y-m-15');
            }

            if($rangeType == 'currCutOff'){
                $date_start = $current_cutoff_start;
                $date_end = $current_cutoff_end;
                
                if($filterType == 'deptName_all'){
                    $queryFilter = "WHERE tadi.tadi_approved_date BETWEEN ? AND ?";

                    $values = [$date_start, $date_end];
                    $bind = "ss";
                }else if($filterType == 'name_Search'){
                    $name = $_POST['name'];
                    $bindName = "%". $name . "%";

                    $queryFilter = "WHERE tadi.tadi_approved_date BETWEEN ? AND ?
                    AND CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`) LIKE ?";

                    $values = [$date_start, $date_end, $bindName];
                    $bind = "sss";
                }
                else if($filterType == 'dept_Search'){
                    $dept = $_POST['dept'];

                    $queryFilter = "WHERE tadi.tadi_approved_date BETWEEN ? AND ?
                    AND `dept`.`SchlDept_CODE` = ?";
                    
                    $values = [$date_start, $date_end, $dept];
                    $bind = "sss";
                }
            }

            if($rangeType == 'prevCutOff'){
                $date_start = $prev_cutoff_start;
                $date_end = $prev_cutoff_end;

                if($filterType == 'deptName_all'){
                    $queryFilter = "WHERE tadi.tadi_approved_date BETWEEN ? AND ?";

                    $values = [$date_start, $date_end];
                    $bind = "ss";
                }else if($filterType == 'name_Search'){
                    $name = $_POST['name'];
                    $bindName = "%". $name . "%";

                    $queryFilter = "WHERE tadi.tadi_approved_date BETWEEN ? AND ?
                    AND CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`) LIKE ?";

                    $values = [$date_start, $date_end, $bindName];
                    $bind = "sss";
                }
                else if($filterType == 'dept_Search'){
                    $dept = $_POST['dept'];

                    $queryFilter = "WHERE tadi.tadi_approved_date BETWEEN ? AND ?
                    AND `dept`.`SchlDept_CODE` = ?";

                    $values = [$date_start, $date_end, $dept];
                    $bind = "sss";
                }
            }

            $qry = "SELECT  
                        CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`) AS prof_name,
                        subj.`SchlAcadSubj_CODE` AS subject_code,
                        subj.`SchlAcadSubj_DESC` AS subject_desc,
                        sec.`SchlAcadSec_NAME` AS section_name,
                        tadi.`schltadi_id`,
                        tadi.`tadi_approved_date` AS tadi_date,
                        tadi.`schltadi_timein` AS time_in,
                        tadi.`schltadi_timeout` AS time_out,
                        TIMEDIFF(tadi.schltadi_timeout, tadi.schltadi_timein) AS duration,
                        tadi.`schltadi_mode` AS mode,
                        tadi.`schltadi_type` AS type,
                        tadi.`schltadi_activity` AS activity,
                        tadi.`schltadi_status` AS status,
                        tadi.schltadi_late_status AS late_status,
                        tadi.schltadi_isconfirm AS approved,
                        CONCAT(info.`SchlEnrollRegStudInfo_LAST_NAME`, ', ', info.`SchlEnrollRegStudInfo_FIRST_NAME`) AS student_name

                    FROM schooltadi tadi

                    LEFT JOIN schoolstudent stud
                        ON tadi.`schlstud_id` = stud.`SchlStudSms_ID`
                    LEFT JOIN schoolenrollmentregistrationstudentinformation info
                        ON stud.`SchlEnrollRegColl_ID` = info.`SchlEnrollReg_ID`
                    LEFT JOIN schoolenrollmentsubjectoffered off
                        ON tadi.`schlenrollsubjoff_id` = off.`SchlEnrollSubjOffSms_ID`
                    LEFT JOIN schoolacademicsubject subj
                        ON off.`SchlAcadSubj_ID` = subj.`SchlAcadSubjSms_ID`
                    LEFT JOIN schoolacademicsection sec
                        ON off.`SchlAcadSec_ID` = sec.`SchlAcadSecSms_ID`
                    LEFT JOIN schoolacademiccourses crse
                        ON off.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID`
                    LEFT JOIN schooldepartment dept
                        ON crse.`SchlDept_ID` = dept.`SchlDeptSms_ID`
                    LEFT JOIN schoolemployee emp
                        ON tadi.`schlprof_id` = emp.`SchlEmpSms_ID`

                    $queryFilter
                    ORDER BY 
                        emp.SchlEmp_LNAME, 
                        subj.SchlAcadSubj_CODE,
                        tadi.tadi_approved_date,
                        tadi.schltadi_timein";

            if ($bind === '' || empty($values)) {
                http_response_code(400);
                $fetch = ['error' => 'Invalid rangeType/filterType'];
                $dbConn->close();
            } else {
                $stmt = $dbConn->prepare($qry);
                $stmt->bind_param($bind, ...$values);
                $stmt->execute();
                $result = $stmt->get_result();
                $fetch = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                $dbConn->close();
            }
            break;
        case 'GET_INSTRUCTOR_LIST_DEPT_SUMMARY':

            if (!isset($_POST['rangeType'], $_POST['dept'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
                exit;
			}

            $rangeType = $_POST['rangeType'];
            $dept = $_POST['dept'];

            $queryFilter = "";
            $bind = "";
            $values = [];

            $today = date('Y-m-d');
            $current_day = date('d');
            $current_month = date('m');
            $current_year = date('Y');

            if ($current_day <= 15) {
                $current_cutoff_start = date('Y-m-01');
                $current_cutoff_end = date('Y-m-15');
                
                // Previous cut-off is 16-end of previous month
                $prev_month_start = new DateTime($current_year . '-' . $current_month . '-01');
                $prev_month_start->modify('-1 month');
                $prev_month_str = $prev_month_start->format('Y-m');
                $prev_cutoff_start = $prev_month_str . '-16';
                $prev_cutoff_end = $prev_month_str . '-' . $prev_month_start->format('t');
            } else {
                $current_cutoff_start = date('Y-m-16');
                $current_cutoff_end = date('Y-m-t');
                
                // Previous cut-off is 1-15 of current month
                $prev_cutoff_start = date('Y-m-01');
                $prev_cutoff_end = date('Y-m-15');
            }

            if($rangeType == 'byDate'){
                $startDate = $_POST['startDate'];
                $endDate = $_POST['endDate'];
                
                $queryFilter = "AND st.tadi_approved_date BETWEEN ? AND ?
                AND `schl_dept`.`SchlDept_CODE` = ?";
                
                $values = [ $staticYear, $staticPeriod, $startDate, $endDate, $dept, 
                            $staticYear, $staticPeriod, $startDate, $endDate, $dept, 
                            $staticYear, $staticPeriod, $startDate, $endDate, $dept, 
                            $staticYear, $staticPeriod, $startDate, $endDate, $dept, 
                            $staticYear, $staticPeriod, $startDate, $endDate, $dept, 
                            $staticYear, $staticPeriod, $dept ];
                $bind = "iisssiisssiisssiisssiisssiis";
            }

            if($rangeType == 'currCutOff'){
                $date_start = $current_cutoff_start;
                $date_end = $current_cutoff_end;
                
                $queryFilter = "AND st.tadi_approved_date BETWEEN ? AND ?
                AND `schl_dept`.`SchlDept_CODE` = ?";
                
                $values = [ $staticYear, $staticPeriod, $date_start, $date_end, $dept, 
                            $staticYear, $staticPeriod, $date_start, $date_end, $dept, 
                            $staticYear, $staticPeriod, $date_start, $date_end, $dept, 
                            $staticYear, $staticPeriod, $date_start, $date_end, $dept, 
                            $staticYear, $staticPeriod, $date_start, $date_end, $dept, 
                            $staticYear, $staticPeriod, $dept ];
                $bind = "iisssiisssiisssiisssiisssiis";
            }

            if($rangeType == 'prevCutOff'){
                $date_start = $prev_cutoff_start;
                $date_end = $prev_cutoff_end;

                $queryFilter = "AND st.tadi_approved_date BETWEEN ? AND ?
                AND `schl_dept`.`SchlDept_CODE` = ?";

                $values = [ $staticYear, $staticPeriod, $date_start, $date_end, $dept, 
                            $staticYear, $staticPeriod, $date_start, $date_end, $dept, 
                            $staticYear, $staticPeriod, $date_start, $date_end, $dept, 
                            $staticYear, $staticPeriod, $date_start, $date_end, $dept, 
                            $staticYear, $staticPeriod, $date_start, $date_end, $dept, 
                            $staticYear, $staticPeriod, $dept ];
                $bind = "iisssiisssiisssiisssiisssiis";
            }

            $qry = "SELECT DISTINCT 
                    schl_dept.`SchlDept_CODE` dept_code,
                    CONCAT(
                        emp.SchlEmp_LNAME,
                        ', ',
                        emp.SchlEmp_FNAME,
                        ' ',
                        emp.SchlEmp_MNAME
                    ) AS prof_name,
                    (SELECT 
                        COUNT(*) 
                    FROM
                        schooltadi st 
                        INNER JOIN schoolenrollmentsubjectoffered seso 
                        ON st.schlenrollsubjoff_id = seso.SchlEnrollSubjOffSms_ID 
                        LEFT JOIN `schoolacademiccourses` `schl_acad_crses` 
                        ON `seso`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID` 
                        LEFT JOIN `schooldepartment` `schl_dept` 
                        ON `schl_acad_crses`.`SchlDept_ID` = `schl_dept`.`SchlDeptSms_ID` 
                    WHERE st.SchlProf_ID = `schl_enr_subj_off`.`SchlProf_ID` 
                        AND st.schltadi_status = 1
                        AND seso.SchlAcadLvl_ID = 2 
                        AND seso.SchlAcadYr_ID = ?
                        AND seso.SchlAcadPrd_ID = ?
                        $queryFilter ) AS verified_count,
                    (SELECT 
                        COUNT(*) 
                    FROM
                        schooltadi st 
                        INNER JOIN schoolenrollmentsubjectoffered seso 
                        ON st.schlenrollsubjoff_id = seso.SchlEnrollSubjOffSms_ID 
                        LEFT JOIN `schoolacademiccourses` `schl_acad_crses` 
                        ON `seso`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID` 
                        LEFT JOIN `schooldepartment` `schl_dept` 
                        ON `schl_acad_crses`.`SchlDept_ID` = `schl_dept`.`SchlDeptSms_ID` 
                    WHERE st.SchlProf_ID = `schl_enr_subj_off`.`SchlProf_ID` 
                        AND st.schltadi_status = 0 
                        AND seso.SchlAcadLvl_ID = 2 
                        AND seso.SchlAcadYr_ID = ?
                        AND seso.SchlAcadPrd_ID = ?
                        $queryFilter ) AS unverified_count,
                    (SELECT 
                        COUNT(*) 
                    FROM
                        schooltadi st 
                        INNER JOIN schoolenrollmentsubjectoffered seso 
                        ON st.schlenrollsubjoff_id = seso.SchlEnrollSubjOffSms_ID 
                        LEFT JOIN `schoolacademiccourses` `schl_acad_crses` 
                        ON `seso`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID` 
                        LEFT JOIN `schooldepartment` `schl_dept` 
                        ON `schl_acad_crses`.`SchlDept_ID` = `schl_dept`.`SchlDeptSms_ID` 
                    WHERE st.SchlProf_ID = `schl_enr_subj_off`.`SchlProf_ID` 
                        AND seso.SchlAcadLvl_ID = 2 
                        AND seso.SchlAcadYr_ID = ?
                        AND seso.SchlAcadPrd_ID = ?
                        $queryFilter ) AS total_count,
                    (SELECT
                        COUNT(*)
                    FROM
                        schooltadi st
                        INNER JOIN schoolenrollmentsubjectoffered seso
                        ON st.schlenrollsubjoff_id = seso.SchlEnrollSubjOffSms_ID
                        LEFT JOIN `schoolacademiccourses` `schl_acad_crses`
                        ON `seso`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID`
                        LEFT JOIN `schooldepartment` `schl_dept`
                        ON `schl_acad_crses`.`SchlDept_ID` = `schl_dept`.`SchlDeptSms_ID`
                    WHERE st.SchlProf_ID = `schl_enr_subj_off`.`SchlProf_ID`
                        AND st.schltadi_status = 1
                        AND st.schltadi_isconfirm = 0
                        AND seso.SchlAcadLvl_ID = 2
                        AND seso.SchlAcadYr_ID = ?
                        AND seso.SchlAcadPrd_ID = ?
                        $queryFilter ) AS to_approved,
                    (SELECT
                        COUNT(*)
                    FROM
                        schooltadi st
                        INNER JOIN schoolenrollmentsubjectoffered seso
                        ON st.schlenrollsubjoff_id = seso.SchlEnrollSubjOffSms_ID
                        LEFT JOIN `schoolacademiccourses` `schl_acad_crses`
                        ON `seso`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID`
                        LEFT JOIN `schooldepartment` `schl_dept`
                        ON `schl_acad_crses`.`SchlDept_ID` = `schl_dept`.`SchlDeptSms_ID`
                    WHERE st.SchlProf_ID = `schl_enr_subj_off`.`SchlProf_ID`
                        AND st.schltadi_status = 1
                        AND st.schltadi_isconfirm = 1
                        AND seso.SchlAcadLvl_ID = 2
                        AND seso.SchlAcadYr_ID = ?
                        AND seso.SchlAcadPrd_ID = ?
                        $queryFilter ) AS approved
                    FROM
                    `schoolenrollmentsubjectoffered` `schl_enr_subj_off` 
                    LEFT JOIN `schoolacademiccourses` `schl_acad_crses` 
                        ON `schl_enr_subj_off`.`SchlAcadCrses_ID` = `schl_acad_crses`.`SchlAcadCrseSms_ID` 
                    LEFT JOIN `schooldepartment` `schl_dept` 
                        ON `schl_acad_crses`.`SchlDept_ID` = `schl_dept`.`SchlDeptSms_ID` 
                    LEFT JOIN schoolemployee AS emp 
                        ON `schl_enr_subj_off`.`SchlProf_ID` = emp.`SchlEmpSms_ID` 
                    WHERE `schl_enr_subj_off`.`SchlAcadLvl_ID` = 2 
                    AND `schl_enr_subj_off`.`SchlAcadYr_ID` = ?
                    AND `schl_enr_subj_off`.`SchlAcadPrd_ID` = ?
                    AND `schl_dept`.`SchlDept_CODE` = ?
                    AND `schl_enr_subj_off`.`SchlEnrollSubjOff_ISACTIVE` = 1 
                    AND emp.`SchlEmp_ID` IS NOT NULL 
                    GROUP BY `schl_enr_subj_off`.`SchlProf_ID`,
                    emp.SchlEmp_LNAME,
                    emp.SchlEmp_FNAME,
                    emp.SchlEmp_MNAME 
                    ORDER BY prof_name ASC 
                    ";

            $stmt = $dbConn->prepare($qry);
            $stmt->bind_param($bind, ...$values);
            $stmt->execute();
            $result = $stmt->get_result();
            $fetch = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            $dbConn->close();
            break;
        case 'GET_ACADEMIC_LEVEL':
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
                    ORDER BY AcadLvl_Name DESC";

            $stmt = $dbConn->prepare($qry); 
            $stmt->execute();
            $result = $stmt->get_result();
            $fetch = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            $dbConn->close();
            break;
        case 'GET_ACADEMIC_YEAR_LEVEL':

            if (!isset($_POST['lvl_id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
                exit;
			}

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
            break;
        case 'GET_ACADEMIC_PERIOD':

            if (!isset($_POST['lvl_id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
                exit;
			}

            $lvlid = $_POST['lvl_id'];

            $qry = "SELECT DISTINCT
                            `schl_acad_prd`.`SchlAcadPrdSms_ID` AS `acad_prd_id`,
                            `schl_acad_prd`.`SchlAcadPrd_NAME` AS `acad_prd_name`,
                            `schl_acad_yr_prd`.`SchlAcadYrPrd_ISOPEN` AS `is_current`
                        FROM `schoolacademicyearperiod` AS `schl_acad_yr_prd`
                        LEFT JOIN `schoolacademicperiod` AS `schl_acad_prd`
                            ON `schl_acad_yr_prd`.`SchlAcadPrd_ID` =  `schl_acad_prd`.`SchlAcadPrdSms_ID`
                        WHERE `schl_acad_yr_prd`.`SchlAcadLvl_ID` = ?
                        AND `schl_acad_yr_prd`.SchlAcadYr_ID = ?
                        AND `schl_acad_yr_prd`.`SchlAcadYrPrd_ISACTIVE` = 1 ";

            $stmt = $dbConn->prepare($qry);
            $stmt->bind_param("ii", $lvlid, $staticYear);
            $stmt->execute();
            $result = $stmt->get_result();
            $fetch = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            $dbConn->close();
            break;
        case 'GET_ACAD_YEAR':

            if (!isset($_POST['lvl_id'],$_POST['prd_id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
                exit;
			}

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
            break;
        case 'GET_INSTRUCTOR_SCHEDULE':

            if (!isset($_POST['SchlProf_ID'],$_POST['semesterFilter'],$_POST['lvlid'],$_POST['acadyr'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
                exit;
			}

            $profId = isset($_POST['SchlProf_ID']) ? intval($_POST['SchlProf_ID']) : 0;
            $semesterFilter = isset($_POST['semesterFilter']) ? trim(strval($_POST['semesterFilter'])) : 'all';
            $useCurrentSemester = in_array(strtolower($semesterFilter), ['current', 'currentsemester', 'currsemester'], true);

            $acadLvlId = isset($_POST['lvlid']) ? intval($_POST['lvlid']) : (isset($_POST['lvl_id']) ? intval($_POST['lvl_id']) : 2);
            $acadYrId = isset($_POST['acadyr']) ? intval($_POST['acadyr']) : (isset($_POST['acadyrid']) ? intval($_POST['acadyrid']) : 22);

            if ($profId <= 0) {
                http_response_code(400);
                $fetch = ['error' => 'Missing or invalid SchlProf_ID'];
            } else {
                $extraWhere = "";
                $bind = 'i';
                $values = [$profId];

                if ($useCurrentSemester) {
                    $currPrdId = get_current_academic_period_id($dbConn, $acadLvlId, $acadYrId);
                    if ($currPrdId === null) {
                        http_response_code(400);
                        $fetch = ['error' => 'No currently-open academic period found for this level/year'];
                        $dbConn->close();
                    } else {
                        $extraWhere = " AND off.`SchlAcadLvl_ID` = ? AND off.`SchlAcadYr_ID` = ? AND off.`SchlAcadPrd_ID` = ?";
                        $bind .= 'iii';
                        $values[] = $acadLvlId;
                        $values[] = $acadYrId;
                        $values[] = $currPrdId;
                    }
                }

                if (!isset($fetch)) {
                $qry = "SELECT
                            t.course_description,
                            TRIM(SUBSTRING_INDEX(t.sched, ' ', 1)) AS day,
                            TRIM(SUBSTRING(t.sched, LOCATE(' ', t.sched) + 1)) AS time,
                            t.section,
                            t.no_of_hours
                        FROM (
                            SELECT
                                subj.`SchlAcadSubj_DESC` AS course_description,
                                sec.`SchlAcadSec_NAME` AS section,
                                IFNULL(off.`SchlEnrollSubjOff_UNIT`, 0) AS no_of_hours,
                                FORMAT_SCHEDULE_STRING(off.`SchlEnrollSubjOff_SCHEDULE_2`) AS sched
                            FROM `schoolenrollmentsubjectoffered` off
                            LEFT JOIN `schoolacademicsubject` subj
                                ON off.`SchlAcadSubj_ID` = subj.`SchlAcadSubjSms_ID`
                            LEFT JOIN `schoolacademicsection` sec
                                ON off.`SchlAcadSec_ID` = sec.`SchlAcadSecSms_ID`
                            WHERE off.`SchlEnrollSubjOff_STATUS` = 1
                                AND off.`SchlEnrollSubjOff_ISACTIVE` = 1
                                AND FIND_IN_SET(?, off.`SchlProf_ID`) > 0
                                $extraWhere
                        ) t
                        ORDER BY t.section, t.course_description";

                $stmt = $dbConn->prepare($qry);
                $stmt->bind_param($bind, ...$values);
                $stmt->execute();
                $result = $stmt->get_result();
                $fetch = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                $dbConn->close();
                }
            }
            break;
        case 'GET_TABULATION':

            if (!isset($_POST['lvlid'], $_POST['prdid'], $_POST['acadyr'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
                exit;
            }

            $lvlid = $_POST['lvlid'];
            $acadyrid = $_POST['acadyr'];
            $prdidRaw = isset($_POST['prdid']) ? trim(strval($_POST['prdid'])) : '';
            if ($prdidRaw === '' || strtolower($prdidRaw) === 'current' || strtolower($prdidRaw) === 'currentsemester' || strtolower($prdidRaw) === 'currsemester' || intval($prdidRaw) <= 0) {
                $resolvedPrdId = get_current_academic_period_id($dbConn, intval($lvlid), intval($acadyrid));
                if ($resolvedPrdId === null) {
                    http_response_code(400);
                    echo json_encode(['error' => 'No currently-open academic period found for this level/year']);
                    $dbConn->close();
                    exit;
                }
                $prdid = $resolvedPrdId;
            } else {
                $prdid = intval($prdidRaw);
            }
            $filterType = $_POST['filterType'];
            $fDate = isset($_POST['startDate']) && $_POST['startDate'] !== '' ? $_POST['startDate'] : date('Y-m-01');
            $lDate = isset($_POST['endDate']) && $_POST['endDate'] !== '' ? $_POST['endDate'] : date('Y-m-t');

            switch($filterType){
                case 'byDept':
                    $dept = $_POST['dept'];
                    $filter = "AND dept.`SchlDept_CODE` = ?";
                    $values = [$fDate, $lDate, $lvlid, $acadyrid, $prdid, $lvlid, $acadyrid, $prdid, $lvlid, $acadyrid, $prdid, $dept];
                    $bind = "ssiiiiiiiiis";
                    break;
                case 'byName':
                    $name = $_POST['name'];
                    $filter = "AND CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`) LIKE ?";
                    $bindName = "%". $name . "%";
                    $values = [$fDate, $lDate, $lvlid, $acadyrid, $prdid, $lvlid, $acadyrid, $prdid, $lvlid, $acadyrid, $prdid, $bindName];
                    $bind = "ssiiiiiiiiis";
                    break;
                case 'all':
                    $filter = "";
                    $values = [$fDate, $lDate, $lvlid, $acadyrid, $prdid, $lvlid, $acadyrid, $prdid, $lvlid, $acadyrid, $prdid];
                    $bind = "ssiiiiiiiii";
                    break;
                default:
                    echo json_encode(['error' => 'Invalid filter type']);
                    exit;
            }

            $qry = "WITH tadi_hours AS (
                        SELECT 
                            t.`schlenrollsubjoff_id`,
                            t.`schlprof_id`,
                            o.`SchlAcadSubj_ID`,
                            o.`SchlAcadCrses_ID`,
                            ROUND(SUM(
                                CASE WHEN t.`tadi_approved_date` BETWEEN ? AND ?
                                THEN TIMESTAMPDIFF(MINUTE, t.`schltadi_timein`, t.`schltadi_timeout`)
                                ELSE 0 END
                            ) / 60, 2) AS filtered_hours,
                            ROUND(SUM(TIMESTAMPDIFF(MINUTE, t.`schltadi_timein`, t.`schltadi_timeout`)) / 60, 2) AS total_accumulated_hours
                        FROM schooltadi t
                        INNER JOIN schoolenrollmentsubjectoffered o
                            ON t.`schlenrollsubjoff_id` = o.`SchlEnrollSubjOffSms_ID`
                        WHERE o.`SchlAcadLvl_ID` = ?
                            AND o.`SchlAcadYr_ID` = ?
                            AND o.`SchlAcadPrd_ID` = ?
                            AND FIND_IN_SET(t.`schlprof_id`, o.`SchlProf_ID`) > 0
                            AND t.`schltadi_status` = 1
                            AND t.`schltadi_isactive` = 1
                            AND t.`schltadi_isconfirm` = 1
                            AND t.`schltadi_timein` IS NOT NULL
                            AND t.`schltadi_timeout` IS NOT NULL
                        GROUP BY t.`schlenrollsubjoff_id`, t.`schlprof_id`, o.`SchlAcadSubj_ID`, o.`SchlAcadCrses_ID`
                    ),
                    enrolled_counts AS (
                        SELECT 
                            o.`SchlEnrollSubjOffSms_ID`,
                            o.`SchlAcadSubj_ID`,
                            o.`SchlAcadCrses_ID`,
                            o.`SchlProf_ID`,
                            COUNT(DISTINCT asmt.`SchlEnrollAssSms_ID`) AS total_enrolled
                        FROM schoolenrollmentsubjectoffered o
                        INNER JOIN schoolenrollmentassessment asmt
                            ON FIND_IN_SET(o.`SchlEnrollSubjOffSms_ID`, asmt.`SchlAcadSubj_ID`) > 0
                            AND asmt.`SchlAcadLvl_ID` = o.`SchlAcadLvl_ID`
                            AND asmt.`SchlAcadYr_ID` = o.`SchlAcadYr_ID`
                            AND asmt.`SchlAcadPrd_ID` = o.`SchlAcadPrd_ID`
                        INNER JOIN schoolstudent stud
                            ON asmt.`SchlStud_ID` = stud.`SchlStudSms_ID`
                        WHERE o.`SchlAcadLvl_ID` = ? AND o.`SchlAcadYr_ID` = ? AND o.`SchlAcadPrd_ID` = ?
                            AND IFNULL(asmt.`SchlEnrollAss_STATUS`, 0) = 1
                            AND IFNULL(asmt.`SchlEnrollWithdrawType_ID`, 0) = 0
                            AND IFNULL(stud.`SchlStud_STATUS`, 0) = 1
                            AND IFNULL(stud.`SchlStud_ISACTIVE`, 0) = 1
                        GROUP BY o.`SchlEnrollSubjOffSms_ID`, o.`SchlAcadSubj_ID`, o.`SchlAcadCrses_ID`, o.`SchlProf_ID`
                    )
                    SELECT
                        MIN(off.SchlEnrollSubjOffSms_ID) AS rec_id,
                        off.SchlProf_ID AS profID,
                        emp.`SchlEmpSms_ID` AS prof_id,
                        CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`) AS prof_name,
                        subj.`SchlAcadSubj_CODE` AS subject_code,
                        subj.`SchlAcadSubj_DESC` AS subject_desc,
                        crse.`SchlAcadCrses_NAME` AS course_name,
                        dept.`SchlDept_CODE` AS dept_code,
                        COUNT(DISTINCT off.`SchlEnrollSubjOffSms_ID`) AS section_count,
                        GROUP_CONCAT(DISTINCT sec.`SchlAcadSec_NAME` ORDER BY sec.`SchlAcadSec_NAME` SEPARATOR ', ') AS sections,
                        IFNULL(MAX(ec.total_enrolled), 0) AS total_enrolled_students,
                        IFNULL(MAX(th.filtered_hours), 0) AS filtered_hours,
                        IFNULL(MAX(th.total_accumulated_hours), 0) AS total_accumulated_hours,
                        subj.`SchlAcadSubj_LEC` AS lec_units,
                        subj.`SchlAcadSubj_LAB` AS lab_units,
                        off.SchlProf_UNIT_HRS AS prof_unit_hrs,
                        off.SchlEnrollSubjOff_UNIT_HRS AS subj_unit_hrs

                    FROM schoolenrollmentsubjectoffered off
                    LEFT JOIN schoolacademicsubject subj ON off.`SchlAcadSubj_ID` = subj.`SchlAcadSubjSms_ID`
                    LEFT JOIN schoolacademicsection sec ON off.`SchlAcadSec_ID` = sec.`SchlAcadSecSms_ID`
                    LEFT JOIN schoolacademiccourses crse ON off.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID`
                    LEFT JOIN schooldepartment dept ON crse.`SchlDept_ID` = dept.`SchlDeptSms_ID`
                    LEFT JOIN schoolemployee emp ON FIND_IN_SET(emp.`SchlEmpSms_ID`, off.`SchlProf_ID`) > 0
                    LEFT JOIN tadi_hours th 
                        ON th.`schlenrollsubjoff_id` = off.`SchlEnrollSubjOffSms_ID`
                        AND th.`schlprof_id` = emp.`SchlEmpSms_ID`
                        AND th.`SchlAcadSubj_ID` = subj.`SchlAcadSubjSms_ID`
                        AND th.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID`
                    LEFT JOIN enrolled_counts ec
                        ON ec.`SchlEnrollSubjOffSms_ID` = off.`SchlEnrollSubjOffSms_ID`
                        AND ec.`SchlAcadSubj_ID` = subj.`SchlAcadSubjSms_ID`
                        AND ec.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID`
                        AND FIND_IN_SET(emp.`SchlEmpSms_ID`, ec.`SchlProf_ID`) > 0

                    WHERE off.`SchlAcadLvl_ID` = ?
                        AND off.`SchlAcadYr_ID` = ?
                        AND off.`SchlAcadPrd_ID` = ?
                        AND off.`SchlEnrollSubjOff_STATUS` = 1
                        AND off.`SchlEnrollSubjOff_ISACTIVE` = 1
                        $filter
                        AND CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`) IS NOT NULL

                    GROUP BY 
                        emp.`SchlEmpSms_ID`,
                        subj.`SchlAcadSubjSms_ID`,
                        crse.`SchlAcadCrseSms_ID`,
                        dept.`SchlDeptSms_ID`,
                        off.SchlEnrollSubjOffSms_ID

                    ORDER BY emp.`SchlEmp_LNAME`, subj.`SchlAcadSubj_CODE`";

                $stmt = $dbConn->prepare($qry);
                $stmt->bind_param($bind,...$values);
                $stmt->execute();
                $result = $stmt->get_result();
                $fetch = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                $dbConn->close();
            break;
        case 'RECORD_TABULATION':
            if(!isset($_SESSION['EMPLOYEE']['ID'],$_POST['prof_id'],$_POST['subj_code'],$_POST['prof_hrs'],$_POST['subj_hrs'])){
                http_response_code(401);
                echo json_encode(["error" => "Missing session data"]);
                exit;
            }

            $profId = $_POST['prof_id'];
            $subjCode = $_POST['subj_code'];
            $profHrs = $_POST['prof_hrs'];
            $subjHrs = $_POST['subj_hrs'];

            if(!ctype_digit((string)$profId) || !ctype_digit((string)$subjCode) || !ctype_digit((string)$profHrs) || !ctype_digit((string)$subjHrs)){
                http_response_code(400);
                echo json_encode(["error" => "Invalid input data"]);
                exit;
            }

            $qry = "INSERT INTO schooltadi_confirmed_tabulation_history (subj_offid, unit_hrs, prof_hrs, prof_id)
                    VALUES (?,?,?,?)";

            $stmt = $dbConn->prepare($qry);

            if (!$stmt) {
                http_response_code(500);
                echo json_encode(["error" => $dbConn->error]);
                exit;
            }

            $stmt->bind_param("iiii", $subjCode, $subjHrs, $profHrs, $profId);

            if (!$stmt->execute()) {
                http_response_code(500);
                echo json_encode(["error" => $stmt->error]);
                $stmt->close();
                exit;
            }

            echo json_encode([
                "success" => true,
            ]);

            $stmt->close();
            break; 
        default:
            http_response_code(400);
            $fetch = ['error' => 'Invalid request type'];
            break;
    }
}

if (!isset($fetch)) {
    echo json_encode(['error' => 'Invalid request type']);
} else {
    echo json_encode($fetch);
}
?>