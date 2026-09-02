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
include('../../shared/logging.php');

function logStudentTadiInfo(mysqli $dbConn, string $access, ?string $errorMessage): void {
    $userId = intval($_SESSION['STUDENT']['ID'] ?? 0);
    logTadiActivity($dbConn, $access, $errorMessage, $userId, 2);
}

function rateLimit(int $window, int $max, string $key, mysqli $dbConn, string $access){
    $rateLimitWindowSec = $window;
    $rateLimitMax = $max;
    $rateLimitKey = $key;
    $now = time();

    if (!isset($_SESSION[$rateLimitKey])) {
        $_SESSION[$rateLimitKey] = ['count' => 1, 'start' => $now];
    } else {
        $elapsed = $now - $_SESSION[$rateLimitKey]['start'];
        if ($elapsed > $rateLimitWindowSec) {
            $_SESSION[$rateLimitKey] = ['count' => 1, 'start' => $now];
        } else {
            $_SESSION[$rateLimitKey]['count']++;
            if ($_SESSION[$rateLimitKey]['count'] > $rateLimitMax) {
                http_response_code(429);
                $fetch['message'] = 'Too many submissions. Please try again later.';
                logStudentTadiInfo($dbConn, $access, $fetch['message']);
                echo json_encode($fetch);
                exit;
            }
        }
    }
}

function sanitizeText(?string $value): string {
    $value = trim((string)$value);
    $value = strip_tags($value);
    return preg_replace('/\s+/', ' ', $value) ?? '';
}

function isSafeAttachmentPath(string $path): bool {
    if ($path === '') {
        return false;
    }

    if (strpos($path, '..') !== false || strpos($path, '\\') !== false || strpos($path, ':') !== false) {
        return false;
    }

    // Allow optional subject folder between the date and filename (some uploads include subj_id)
    return (bool)preg_match('/^attachment\/[A-Za-z0-9._-]+\/\d{4}-\d{2}-\d{2}(?:\/[A-Za-z0-9._-]+)?\/[A-Za-z0-9._-]+$/', $path);
}

function cleanCommaList($str) {
    if ($str === null || $str === '') {
        return '';
    }

    // Split on comma, trim each piece, drop empty entries
    $parts = array_filter(
        array_map('trim', explode(',', $str)),
        function ($val) {
            return $val !== '';
        }
    );

    // Re-index and join back into a clean comma-separated string
    return implode(',', array_values($parts));
}

$fetch = "";

$type = $_POST['type'] ?? '';
$queryType = ['GET_SCHOOL_YEAR', 'GET_ACADEMIC_PRD', 'GET_YEAR_LEVEL', 'GET_ACADEMIC_LEVEL', 'GET_SUBJECT_LIST', 'GET_SUBMITTED_REC', 'GET_IMAGE', 'REVERT_SUBMISSION'];

if($_SESSION['STUDENT'] && in_array($type, $queryType, true)) {
    switch ($type) {

        case 'GET_SUBJECT_LIST':
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
                                                        WHERE `schl_stud`.`SchlStudSms_ID` = ?
                                                            AND `schl_enr_as`.`SchlAcadLvl_ID` = ?
                                                            AND `schl_enr_as`.`SchlAcadYr_ID` = ?
                                                            AND `schl_enr_as`.`SchlAcadPrd_ID` = ?
                                                            AND `schl_enr_reg`.`SchlAcadLvl_ID` = ?";

            $stmt_subj_list = $dbConn->prepare($qry_get_subj_list);
            $stmt_subj_list->bind_param("iiiii", $USERID, $LVLID, $YRID, $PRDID, $LVLID);
            $stmt_subj_list->execute();
            $rreg = $stmt_subj_list->get_result();
            $stud_subj_list = $rreg->fetch_assoc();
            $stmt_subj_list->close();

            $subj_list = $stud_subj_list['schl_acad_subj_id'];
            $subj_list = cleanCommaList($subj_list);

            $qry = "SELECT `schl_enr_subj_off`.`SchlEnrollSubjOffSms_ID` AS `subj_id`,
                        `schl_acad_subj`.`SchlAcadSubj_CODE` AS `subj_code`,
                        `schl_acad_subj`.`SchlAcadSubj_desc` AS `subj_desc`,
                        `schl_enr_subj_off`.`SchlEnrollSubjOff_UNIT` AS `schl_subj_unit`,
                        `schl_enr_subj_off`.`SchlProf_ID` AS `prof_id`,
                        `schl_enr_subj_off`.`SchlAcadYr_ID` AS `acad_year_id`,
                        `schl_enr_subj_off`.`SchlAcadLvl_ID` AS `acad_lvl_id`,
                        `schl_enr_subj_off`.`SchlAcadPrd_ID` AS `acad_prd_id`,
                        (
                            SELECT GROUP_CONCAT(
                                CONCAT(`schl_emp`.`SchlEmp_FNAME`, ' ', `schl_emp`.`SchlEmp_LNAME`)
                                ORDER BY FIND_IN_SET(`schl_emp`.`SchlEmpSms_ID`, `schl_enr_subj_off`.`SchlProf_ID`)
                                SEPARATOR ' / '
                            )
                            FROM `schoolemployee` `schl_emp`
                            WHERE FIND_IN_SET(`schl_emp`.`SchlEmpSms_ID`, `schl_enr_subj_off`.`SchlProf_ID`) > 0
                        ) AS `prof_name`,
                        COUNT(studrec.schltadi_id) AS record_count_today
                    FROM schoolenrollmentsubjectoffered schl_enr_subj_off
                    LEFT JOIN schoolacademicsubject schl_acad_subj
                        ON schl_enr_subj_off.SchlAcadSubj_ID = schl_acad_subj.SchlAcadSubjSms_ID
                    LEFT JOIN schooltadi studrec
                        ON schl_enr_subj_off.SchlEnrollSubjOffSms_ID = studrec.schlenrollsubjoff_id
                        AND DATE(studrec.schltadi_actual_date) = CURDATE()
                        AND studrec.schltadi_isactive = 1
                    WHERE 
                        schl_enr_subj_off.SchlEnrollSubjOffSms_ID IN ($subj_list)
                    GROUP BY schl_enr_subj_off.SchlEnrollSubjOffSms_ID";
            
            $rreg = $dbConn->query($qry);
            $fetch = $rreg->fetch_all(MYSQLI_ASSOC);

            foreach ($fetch as &$row) {
                $row['user_id'] = $USERID;
                $row['subj_code'] = sanitizeText($row['subj_code'] ?? '');
                $row['subj_desc'] = sanitizeText($row['subj_desc'] ?? '');
                $row['prof_name'] = sanitizeText($row['prof_name'] ?? '');
            }
            unset($row);

            logStudentTadiInfo($dbConn, 'student.GET_SUBJECT_LIST', null);
        break;

        case 'GET_SUBMITTED_REC':

            if (!isset($_POST['subj_Id'], $_POST['prof_Id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
                exit;
            }

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
                        schl_tadi.schlenrollsubjoff_id AS sub_off_id,
                        schl_tadi.SchlProf_ID,
                        schl_tadi.schlstud_id,
                        CONCAT(schl_reg_stud.SchlEnrollRegStudInfo_LAST_NAME, ', ', schl_reg_stud.SchlEnrollRegStudInfo_FIRST_NAME) AS stud_name,
                        acad_sec.SchlAcadSec_DESC AS section
                    FROM `schooltadi` AS schl_tadi 
                    
                    LEFT JOIN `schoolstudent` AS schl_stud 
                        ON schl_tadi.`schlstud_id` = schl_stud.`SchlStudSms_ID` 

                    LEFT JOIN `schoolenrollmentregistration` AS schl_enr_reg 
                        ON schl_stud.`SchlEnrollRegColl_ID` = schl_enr_reg.`SchlEnrollRegSms_ID` 

                    LEFT JOIN `schoolenrollmentregistrationstudentinformation` AS schl_reg_stud 
                        ON schl_enr_reg.`SchlEnrollRegSms_ID` = `schl_reg_stud`.`SchlEnrollReg_ID` 

                    LEFT JOIN schoolenrollmentsubjectoffered AS subj_off
                        ON schl_tadi.schlenrollsubjoff_id = subj_off.SchlEnrollSubjOffSms_ID

                    LEFT JOIN schoolacademicsection AS acad_sec
                        ON subj_off.SchlAcadSec_ID = acad_sec.SchlAcadSecSms_ID

                    WHERE schl_tadi.`schlprof_id` IN (?)
                    AND schl_tadi.`schlenrollsubjoff_id` =  ?
                    AND schltadi_isactive = 1
                    -- AND `schl_tadi`.`schlstud_id` = ?
                    AND `schltadi_actual_date` = CURDATE()
                    ORDER BY schl_tadi.`schltadi_date`, schl_tadi.`schltadi_timein`";
            
            $stmt = $dbConn->prepare($qry);
            $stmt->bind_param("ii",$prof_Id,$subj_Id);
            $stmt->execute();
            $result = $stmt->get_result();
            $fetch = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            foreach ($fetch as &$row) {
                $row['tadi_act'] = sanitizeText($row['tadi_act'] ?? '');
                $row['tadi_modeType'] = sanitizeText($row['tadi_modeType'] ?? '');
            }
            unset($row);

            logStudentTadiInfo($dbConn, 'student.GET_SUBMITTED_REC', null);
        break;

        case 'GET_IMAGE':
            rateLimit(60, 5, 'get_image_rate_limit', $dbConn, 'student.GET_IMAGE');

            if (!isset($_POST['prof_id'], $_POST['tadi_id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
                exit;
            }

            $prof_id = $_POST['prof_id'];
            $REC_ID = $_POST['tadi_id'];

            $qry = "SELECT 
                        `startschltadi_filepath` AS `starttadi_filepath`,
                        `starttadi_exifDate` AS startexif_date,
                        `starttadi_exifTime` AS startexif_time,
                        `endschltadi_filepath` AS endtadi_filepath,
                        `endtadi_exifDate` AS endexif_date,
                        `starttadi_exifTime` AS endexif_time,
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

            if (!$fetch || empty($fetch['starttadi_filepath'])) {
                $fetch = ['message' => 'Image not found.'];
                logStudentTadiInfo($dbConn, 'student.GET_IMAGE', $fetch['message']);
            } elseif (!isSafeAttachmentPath($fetch['starttadi_filepath'])) {
                $fetch = ['message' => 'Invalid image path.'];
                logStudentTadiInfo($dbConn, 'student.GET_IMAGE', $fetch['message']);
            } else {
                $publicPath = __DIR__ . '/../../../../../../public/' . $fetch['starttadi_filepath'];
                if (!is_file($publicPath)) {
                    $fetch['message'] = 'Image file missing on server.';
                    logStudentTadiInfo($dbConn, 'student.GET_IMAGE', $fetch['message']);
                } else {
                    logStudentTadiInfo($dbConn, 'student.GET_IMAGE', null);
                }
            }
        break;

        case 'REVERT_SUBMISSION':
            $tadi_id = intval($_POST['tadi_id'] ?? 0);
            $subj_id = intval($_POST['subj_id'] ?? 0);
            $prof_id = intval($_POST['prof_id'] ?? 0);

            $session_stud_id = intval($_SESSION['STUDENT']['ID'] ?? 0);

            if($tadi_id <= 0 || $subj_id <= 0 || $prof_id <= 0 || $session_stud_id <= 0){
                $fetch = ['success' => false, 'message' => 'Missing required information.'];
                logStudentTadiInfo($dbConn, 'student.REVERT_SUBMISSION', $fetch['message']);
            } else {
                $verify_stmt = $dbConn->prepare(
                    "SELECT schltadi_id FROM schooltadi 
                    WHERE schltadi_id = ? AND schlstud_id = ? AND schltadi_isactive = 1"
                );
                $verify_stmt->bind_param("ii", $tadi_id, $session_stud_id);
                $verify_stmt->execute();
                $verify_stmt->store_result();

                if($verify_stmt->num_rows === 0){
                    $fetch = ['success' => false, 'message' => 'Unauthorized action.'];
                    $verify_stmt->close();
                    logStudentTadiInfo($dbConn, 'student.REVERT_SUBMISSION', $fetch['message']);
                } else {
                    $verify_stmt->close();

                    $qry = "UPDATE schooltadi tadi
                            SET tadi.schltadi_isactive = 0
                            WHERE tadi.schltadi_id = ?
                                AND tadi.schlenrollsubjoff_id = ?
                                AND tadi.schlstud_id = ?
                                AND tadi.schlprof_id = ?";
                    
                    $stmt = $dbConn->prepare($qry);
                    $stmt->bind_param("iiii", $tadi_id, $subj_id, $session_stud_id, $prof_id);
                    $executed = $stmt->execute();
                    $affected = $stmt->affected_rows;
                    $stmt->close();

                    if($executed && $affected > 0){
                        $fetch = ['success' => true, 'message' => 'Record successfully deleted.'];
                        logStudentTadiInfo($dbConn, 'student.REVERT_SUBMISSION', null);
                    } else {
                        $fetch = ['success' => false, 'message' => 'Failed to delete record. It may have already been removed.'];
                        logStudentTadiInfo($dbConn, 'student.REVERT_SUBMISSION', $fetch['message']);
                    }
                }
            }
        break;

        default:
            $fetch = ['message' => 'Invalid request type.'];
            logStudentTadiInfo($dbConn, $type, $fetch['message']);
            echo json_encode($fetch);
            exit;
    }

}else{
    $fetch = ['message' => 'Unauthorized access.'];
    logStudentTadiInfo($dbConn, $type, $fetch['message']);
    echo json_encode($fetch);
    exit;
}

$dbConn->close();
echo json_encode($fetch);
