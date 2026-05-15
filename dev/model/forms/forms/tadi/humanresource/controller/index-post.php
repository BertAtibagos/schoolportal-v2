<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');

// PHP error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
include('../../../../../configuration/connection-config.php');

$type = $_POST['type'];

if($type == 'GET_ALL_TOTAL'){

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
}

if($type == 'GET_TOTAL_PER_MONTH'){

    $qry = "SELECT 
                MONTHNAME(`schltadi_date`) AS month_name,
                SUM(schltadi_status = 1) AS verified,
                SUM(schltadi_status = 0) AS unverified,
                COUNT(*) AS total
            FROM schooltadi
            GROUP BY MONTH(`schltadi_date`)
            ORDER BY MIN(`schltadi_date`)";
    
    $stmt = $dbConn->prepare($qry);
    $stmt->execute();
    $result = $stmt->get_result();
    $fetch = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $dbConn->close();
}

if($type == 'GET_TOTAL_PER_CUTOFF'){

    $qry = "SELECT 
                CONCAT(MONTHNAME(`schltadi_date`), ' ', 
                    CASE 
                        WHEN DAY(`schltadi_date`) <= 15 THEN '1-15'
                        ELSE CONCAT('16-', DAY(LAST_DAY(`schltadi_date`)))
                    END) AS cutoff_period,
                SUM(schltadi_status = 1) AS verified,
                SUM(schltadi_status = 0) AS unverified,
                COUNT(*) AS total
            FROM schooltadi
            GROUP BY YEAR(`schltadi_date`), MONTH(`schltadi_date`),
                    CASE 
                        WHEN DAY(`schltadi_date`) <= 15 THEN 1
                        ELSE 2
                    END
            ORDER BY YEAR(`schltadi_date`), MONTH(`schltadi_date`),
                    CASE 
                        WHEN DAY(`schltadi_date`) <= 15 THEN 1
                        ELSE 2
                    END";
    
    $stmt = $dbConn->prepare($qry);
    $stmt->execute();
    $result = $stmt->get_result();
    $fetch = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $dbConn->close();
}

if($type == 'GET_ALL_PROG_TOTAL'){

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
                AND off.`SchlAcadYr_ID` = 19 
                AND off.`SchlAcadPrd_ID` = 5  
            GROUP BY dept.`SchlDept_NAME` 
            ORDER BY unverified_count DESC ";

    $stmt = $dbConn->prepare($qry);
    $stmt->execute();
    $result = $stmt->get_result();
    $fetch = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $dbConn->close();

}

if($type == 'GET_TADI_DETAILS_BY_CUTOFF'){
    
    $rangeType = $_POST['rangeType'];
    $filterType = $_POST['filterType'];

    $queryFilter = "";
    $binding = "";

    if($rangeType == 'byDate'){
        if($filterType == 'deptName_all'){
            $startDate = $_POST['startDate'];
            $endDate = $_POST['endDate'];

            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?";

            $values = [$startDate, $endDate];
            $bind = "ss";
        }else if($filterType == 'name_Search'){
            $startDate = $_POST['startDate'];
            $endDate = $_POST['endDate'];
            $name = $_POST['name'];
            $bindName = "%". $name . "%";

            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?
            AND CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`) LIKE ?";

            $values = [$startDate, $endDate, $bindName];
            $bind = "sss";
        }
        else if($filterType == 'dept_Search'){
            $startDate = $_POST['startDate'];
            $endDate = $_POST['endDate'];
            $dept = $_POST['dept'];

            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?
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
            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?";

            $values = [$date_start, $date_end];
            $bind = "ss";
        }else if($filterType == 'name_Search'){
            $name = $_POST['name'];
            $bindName = "%". $name . "%";

            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?
            AND CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`) LIKE ?";

            $values = [$date_start, $date_end, $bindName];
            $bind = "sss";
        }
        else if($filterType == 'dept_Search'){
            $dept = $_POST['dept'];

            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?
            AND `dept`.`SchlDept_CODE` = ?";
            
            $values = [$date_start, $date_end, $dept];
            $bind = "sss";
        }
    }

    if($rangeType == 'prevCutOff'){
        $date_start = $prev_cutoff_start;
        $date_end = $prev_cutoff_end;

        if($filterType == 'deptName_all'){
            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?";

            $values = [$date_start, $date_end];
            $bind = "ss";
        }else if($filterType == 'name_Search'){
            $name = $_POST['name'];
            $bindName = "%". $name . "%";

            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?
            AND CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`) LIKE ?";

            $values = [$date_start, $date_end, $bindName];
            $bind = "sss";
        }
        else if($filterType == 'dept_Search'){
            $dept = $_POST['dept'];

            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?
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
				tadi.`schltadi_date` AS tadi_date,
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
                tadi.schltadi_date,
                tadi.schltadi_timein";

    $stmt = $dbConn->prepare($qry);
    $stmt->bind_param($bind, ...$values);
    $stmt->execute();
    $result = $stmt->get_result();
    $fetch = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $dbConn->close();
}

if($type == 'GET_INSTRUCTOR_LIST_DEPT_SUMMARY'){

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
        
        $queryFilter = "AND st.schltadi_date BETWEEN ? AND ?
        AND `schl_dept`.`SchlDept_CODE` = ?";
        
        $values = [$startDate, $endDate, $dept, $startDate, $endDate, $dept, $startDate, $endDate, $dept, $startDate, $endDate, $dept, $startDate, $endDate, $dept, $dept];
        $bind = "ssssssssssssssss";
    }

    if($rangeType == 'currCutOff'){
        $date_start = $current_cutoff_start;
        $date_end = $current_cutoff_end;
        
        $queryFilter = "AND st.schltadi_date BETWEEN ? AND ?
        AND `schl_dept`.`SchlDept_CODE` = ?";
        
        $values = [$date_start, $date_end, $dept, $date_start, $date_end, $dept, $date_start, $date_end, $dept, $date_start, $date_end, $dept, $date_start, $date_end, $dept, $dept];
        $bind = "ssssssssssssssss";
    }

    if($rangeType == 'prevCutOff'){
        $date_start = $prev_cutoff_start;
        $date_end = $prev_cutoff_end;

        $queryFilter = "AND st.schltadi_date BETWEEN ? AND ?
        AND `schl_dept`.`SchlDept_CODE` = ?";

        $values = [$date_start, $date_end, $dept, $date_start, $date_end, $dept, $date_start, $date_end, $dept, $date_start, $date_end, $dept, $date_start, $date_end, $dept, $dept];
        $bind = "ssssssssssssssss";
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
                AND seso.SchlAcadYr_ID = 19 
                AND seso.SchlAcadPrd_ID = 6
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
                AND seso.SchlAcadYr_ID = 19 
                AND seso.SchlAcadPrd_ID = 6
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
                AND seso.SchlAcadYr_ID = 19 
                AND seso.SchlAcadPrd_ID = 6
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
                AND seso.SchlAcadYr_ID = 19
                AND seso.SchlAcadPrd_ID = 6
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
                AND seso.SchlAcadYr_ID = 19
                AND seso.SchlAcadPrd_ID = 6
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
            AND `schl_enr_subj_off`.`SchlAcadYr_ID` = 19 
            AND `schl_enr_subj_off`.`SchlAcadPrd_ID` = 6
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
}

if($type == 'GET_ACADEMIC_LEVEL'){

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
                    `schl_acad_prd`.`SchlAcadPrd_NAME` AS `acad_prd_name`,
                     `schl_acad_yr_prd`.`SchlAcadYrPrd_ISOPEN` AS `is_current`
                FROM `schoolacademicyearperiod` AS `schl_acad_yr_prd`
                LEFT JOIN `schoolacademicperiod` AS `schl_acad_prd`
                    ON `schl_acad_yr_prd`.`SchlAcadPrd_ID` =  `schl_acad_prd`.`SchlAcadPrdSms_ID`
                WHERE `schl_acad_yr_prd`.`SchlAcadLvl_ID` = ?
                AND `schl_acad_yr_prd`.SchlAcadYr_ID = 19
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

if($type == "GET_TABULATION"){
    $lvlid = $_POST['lvlid'];
    $acadyrid = $_POST['acadyr'];
    $prdid = $_POST['prdid'];
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
                    t.`schlprof_id`,
                    o.`SchlAcadSubj_ID`,
                    o.`SchlAcadCrses_ID`,
                    ROUND(SUM(
                        CASE WHEN t.`schltadi_date` BETWEEN ? AND ?
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
                    AND t.`schltadi_timein` IS NOT NULL
                    AND t.`schltadi_timeout` IS NOT NULL
                    AND t.`schltadi_isconfirm` = 1
                    AND t.`schltadi_status` = 1
                GROUP BY t.`schlprof_id`, o.`SchlAcadSubj_ID`, o.`SchlAcadCrses_ID`
            ),
            enrolled_counts AS (
                SELECT 
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
                GROUP BY o.`SchlAcadSubj_ID`, o.`SchlAcadCrses_ID`, o.`SchlProf_ID`
            )
            SELECT  
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
                subj.`SchlAcadSubj_LAB` AS lab_units

            FROM schoolenrollmentsubjectoffered off
            LEFT JOIN schoolacademicsubject subj ON off.`SchlAcadSubj_ID` = subj.`SchlAcadSubjSms_ID`
            LEFT JOIN schoolacademicsection sec ON off.`SchlAcadSec_ID` = sec.`SchlAcadSecSms_ID`
            LEFT JOIN schoolacademiccourses crse ON off.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID`
            LEFT JOIN schooldepartment dept ON crse.`SchlDept_ID` = dept.`SchlDeptSms_ID`
            LEFT JOIN schoolemployee emp ON FIND_IN_SET(emp.`SchlEmpSms_ID`, off.`SchlProf_ID`) > 0
            LEFT JOIN tadi_hours th 
                ON th.`schlprof_id` = emp.`SchlEmpSms_ID`
                AND th.`SchlAcadSubj_ID` = subj.`SchlAcadSubjSms_ID`
                AND th.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID`
            LEFT JOIN enrolled_counts ec
                ON ec.`SchlAcadSubj_ID` = subj.`SchlAcadSubjSms_ID`
                AND ec.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID`
                AND FIND_IN_SET(emp.`SchlEmpSms_ID`, ec.`SchlProf_ID`) > 0

            WHERE off.`SchlAcadLvl_ID` = ?
                AND off.`SchlAcadYr_ID` = ?
                AND off.`SchlAcadPrd_ID` = ?
                AND off.`SchlEnrollSubjOff_STATUS` = 1
                AND off.`SchlEnrollSubjOff_ISACTIVE` = 1
                $filter

            GROUP BY 
                emp.`SchlEmpSms_ID`,
                subj.`SchlAcadSubjSms_ID`,
                crse.`SchlAcadCrseSms_ID`,
                dept.`SchlDeptSms_ID`

            ORDER BY emp.`SchlEmp_LNAME`, subj.`SchlAcadSubj_CODE`";

        $stmt = $dbConn->prepare($qry);
        $stmt->bind_param($bind,...$values);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $dbConn->close();
}

if (!isset($fetch)) {
    echo json_encode(['error' => 'Invalid request type']);
} else {
    echo json_encode($fetch);
}
?>