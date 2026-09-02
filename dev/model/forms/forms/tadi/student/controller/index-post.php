<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');

ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
include('../../../../../configuration/connection-config.php');
include('../../shared/logging.php');

$fetch = ['success' => false];

function logStudentTadiSubmit(mysqli $dbConn, int $userId, ?string $errorMessage): void {
    logTadiActivity($dbConn, 'student.SUBMIT_TADI', $errorMessage, $userId, 2);
}

function normalizeTimeInput(string $input): ?string {
    $input = trim($input);
    $formats = ['H:i', 'H:i:s'];

    foreach ($formats as $format) {
        $dt = DateTime::createFromFormat($format, $input);
        if ($dt instanceof DateTime && $dt->format($format) === $input) {
            return $dt->format('H:i:s');
        }
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type']) && $_POST['type'] === 'SUBMIT_TADI' && isset($_SESSION['STUDENT'])) {
    $STUDID = $_SESSION['STUDENT']['ID'] ?? 0;
    $LVLID = $_SESSION['STUDENT']['LVLID'] ?? 0;
    $YRID = $_SESSION['STUDENT']['YRID'] ?? 0;
    $PRDID = $_SESSION['STUDENT']['PRDID'] ?? 0;

    if (!$STUDID || !$LVLID || !$YRID || !$PRDID) {
        $fetch['message'] = "Invalid session. Please log in again.";
        logStudentTadiSubmit($dbConn, (int)$STUDID, $fetch['message']);
        echo json_encode($fetch);
        exit;
    }

    $rateLimitWindowSec = 300;
    $rateLimitMax = 5;
    $rateLimitKey = 'rl_tadi_submit';
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
                logStudentTadiSubmit($dbConn, (int)$STUDID, $fetch['message']);
                echo json_encode($fetch);
                exit;
            }
        }
    }

    try {
        $rawTimeIn = $_POST['classStartDateTime'] ?? '';
        $rawTimeOut = $_POST['classEndDateTime'] ?? '';
        $normalizedTimeIn = normalizeTimeInput($rawTimeIn);
        $normalizedTimeOut = normalizeTimeInput($rawTimeOut);

        if ($normalizedTimeIn === null || $normalizedTimeOut === null) {
            $fetch['message'] = "Invalid time format. Please use HH:MM or HH:MM:SS.";
            logStudentTadiSubmit($dbConn, (int)$STUDID, $fetch['message']);
            echo json_encode($fetch);
            exit;
        }

        $prof_id = $dbConn->real_escape_string($_POST['instructor']);
        $schltadi_mode_raw = $_POST['learning_delivery_modalities'] ?? '';
        $allowedModes = ['online_learning', 'onsite_learning'];
        if (!in_array($schltadi_mode_raw, $allowedModes, true)) {
            $fetch['message'] = "Invalid learning delivery modality.";
            logStudentTadiSubmit($dbConn, (int)$STUDID, $fetch['message']);
            echo json_encode($fetch);
            exit;
        }

        $schltadi_mode = $dbConn->real_escape_string($schltadi_mode_raw);
        $schltadi_type = $dbConn->real_escape_string($_POST['session_type']);

        $schltadi_date_raw = trim($_POST['classDate'] ?? '');
        $manilaTz = new DateTimeZone('Asia/Manila');
        $schltadi_date_obj = DateTimeImmutable::createFromFormat('Y-m-d', $schltadi_date_raw, $manilaTz);

        if (!($schltadi_date_obj instanceof DateTimeImmutable) || $schltadi_date_obj->format('Y-m-d') !== $schltadi_date_raw) {
            $fetch['message'] = "Invalid class date format.";
            logStudentTadiSubmit($dbConn, (int)$STUDID, $fetch['message']);
            echo json_encode($fetch);
            exit;
        }

        $today = DateTimeImmutable::createFromFormat(
            'Y-m-d',
            (new DateTimeImmutable('now', $manilaTz))->format('Y-m-d'),
            $manilaTz
        );
        $oldestAllowedDate = $today->sub(new DateInterval('P3D'));
        if ($schltadi_date_obj < $oldestAllowedDate) {
            $fetch['message'] = "Class date is past due. You can only submit within 3 days from today.";
            logStudentTadiSubmit($dbConn, (int)$STUDID, $fetch['message']);
            echo json_encode($fetch);
            exit;
        }
        // Reject future dates
        if ($schltadi_date_obj > $today) {
            $fetch['message'] = "Class date cannot be in the future.";
            logStudentTadiSubmit($dbConn, (int)$STUDID, $fetch['message']);
            echo json_encode($fetch);
            exit;
        }

        $schltadi_date = $dbConn->real_escape_string($schltadi_date_raw);
        $schltadi_timein = $normalizedTimeIn;
        $schltadi_timeout = $normalizedTimeOut;
        $classInst = $_POST['classInst'] ?? [];       // default to empty array, not empty string
        $classInst = array_map('intval', $classInst); // sanitize each ID
        $schltadi_class_instruct = json_encode($classInst);
        $schltadi_activity = $dbConn->real_escape_string($_POST['comments']);
        $subj_id_raw = $_POST['subjoff_id'] ?? '';
        if (!ctype_digit((string)$subj_id_raw) || (int)$subj_id_raw <= 0) {
            $fetch['message'] = "Invalid subject ID.";
            logStudentTadiSubmit($dbConn, (int)$STUDID, $fetch['message']);
            echo json_encode($fetch);
            exit;
        }

        $subj_id = $dbConn->real_escape_string($subj_id_raw);

        $schltadi_late_status = isset($_POST['late_class_date']) && !empty($_POST['late_class_date']) ? 1 : 0;
        $schltadi_late_date = $schltadi_late_status ? $dbConn->real_escape_string($_POST['late_class_date']) : null;
        $schltadi_late_reason = $schltadi_late_status && isset($_POST['late_reason']) ? $dbConn->real_escape_string($_POST['late_reason']) : null;
        $schltadi_mkup_date = ($schltadi_type === 'makeup' && isset($_POST['makeup_class_date']) && !empty($_POST['makeup_class_date'])) ? $dbConn->real_escape_string($_POST['makeup_class_date']) : null;


        $check_sql = "SELECT COUNT(*) as count 
                      FROM schooltadi 
                      WHERE schlenrollsubjoff_id = ?
                      AND schlprof_id = ? 
                      AND schltadi_isactive = 1
                      AND DATE(schltadi_actual_date) = ?";

        $check_stmt = $dbConn->prepare($check_sql);
        if ($check_stmt === false) {
            throw new Exception('Prepare failed: ' . $dbConn->error);
        }

        $check_stmt->bind_param('iis', $subj_id, $prof_id, $schltadi_date);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();
        $count = (int)$count;

        if ($count >= 3) {
            $fetch['message'] = "You have already submitted 3 TADIs today.";
            logStudentTadiSubmit($dbConn, (int)$STUDID, $fetch['message']);
            echo json_encode($fetch);
            exit;
        }

        $overlapTimeSameProf = "SELECT
                                    CONCAT(`SchlEnrollRegStudInfo_LAST_NAME`, ', ', `SchlEnrollRegStudInfo_FIRST_NAME`,' ',`SchlEnrollRegStudInfo_MIDDLE_NAME`) AS stud_name,
                                    tadi.`schltadi_date` AS tadi_date,
                                    tadi.`schltadi_timein` AS tadi_timeIn,
                                    tadi.`schltadi_timeout` AS tadi_timeOut,
                                    sec.SchlAcadSec_DESC AS section,
                                    subj.SchlAcadSubj_DESC AS subject_name
                                FROM schooltadi as tadi
                                LEFT JOIN `schoolstudent` AS schl_stud
                                    ON tadi.`schlstud_id` = schl_stud.`SchlStudSms_ID`

                                LEFT JOIN `schoolenrollmentregistration` AS schl_enr_reg
                                    ON schl_stud.`SchlEnrollRegColl_ID` = schl_enr_reg.`SchlEnrollRegSms_ID`

                                LEFT JOIN `schoolenrollmentregistrationstudentinformation` AS schl_reg_stud
                                    ON schl_enr_reg.`SchlEnrollRegSms_ID` = `schl_reg_stud`.`SchlEnrollReg_ID`
                                
                                LEFT JOIN schoolenrollmentsubjectoffered off
                                    ON tadi.`schlenrollsubjoff_id` = off.`SchlEnrollSubjOffSms_ID`

                                LEFT JOIN schoolacademicsubject subj
                                    ON off.`SchlAcadSubj_ID` = subj.`SchlAcadSubjSms_ID`

                                LEFT JOIN schoolacademicsection sec
                                    ON off.`SchlAcadSec_ID` = sec.`SchlAcadSecSms_ID`

                                WHERE tadi.schlprof_id = ?
                                AND tadi.schltadi_isactive = 1
                                AND DATE(tadi.schltadi_date) = ?
                                AND tadi.schltadi_timein < ?
                                AND tadi.schltadi_timeout > ?";

        $overlap_stmt = $dbConn->prepare($overlapTimeSameProf);
        if ($overlap_stmt === false) {
            throw new Exception('Prepare failed: ' . $dbConn->error);
        }

        $overlap_stmt->bind_param(
            'isss',
            $prof_id,
            $schltadi_date,
            $schltadi_timeout,
            $schltadi_timein
        );

        $overlap_stmt->execute();
        $overlap_stmt->bind_result($stud_name, $tadi_date, $tadi_timeIn, $tadi_timeOut, $section, $subject_name);
        $hasOverlap = false;
        if ($overlap_stmt->fetch()) {
            $hasOverlap = true;
        }
        $overlap_stmt->close();

        if ($hasOverlap) {
            $fetch['isoverlap'] = true;
            $fetch['message'] = "A TADI with an overlapping time range already exists for this instructor.";
            $fetch['overlap_details'] = [
                    'student_name' => $stud_name,
                    'date'         => $tadi_date,
                    'time_in'      => $tadi_timeIn,
                    'time_out'     => $tadi_timeOut,
                    'section'      => $section,
                    'subject_name' => $subject_name
                ];
            logStudentTadiSubmit($dbConn, (int)$STUDID, $fetch['message']);
            echo json_encode($fetch);
            exit;
        }

        $currDate = date("Y-m-d H:i:s");
        
        $stmt = $dbConn->prepare("INSERT INTO schooltadi 
                (schltadi_actual_date,
                schltadi_mode, 
                schltadi_type, 
                schltadi_date, 
                schltadi_timein, 
                schltadi_timeout,
                schltadi_class_instruct,
                schltadi_activity, 
                schltadi_isactive, 
                schltadi_status,
                schlstud_id, 
                schlacadlvl_id, 
                schlacadyr_id,
                schlprof_id, 
                schlenrollsubjoff_id, 
                schlacadprd_id, 
                schltadi_late_status,
                schltadi_late_date,
                schltadi_late_reason,
                schltadi_mkup_date)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $isactive = 1;
        $status = 0;
        $isupdated = 0;

        $stmt->bind_param(
            "ssssssssiiiiiiiiisss",
            $currDate,
            $schltadi_mode,
            $schltadi_type,
            $schltadi_date,
            $schltadi_timein,
            $schltadi_timeout,
            $schltadi_class_instruct,
            $schltadi_activity,
            $isactive,
            $status,
            $STUDID,
            $LVLID,
            $YRID,
            $prof_id,
            $subj_id,
            $PRDID,
            $schltadi_late_status,
            $schltadi_late_date,
            $schltadi_late_reason,
            $schltadi_mkup_date
        );

        if ($stmt->execute()) {
            $fetch['success'] = true;
            $fetch['message'] = "TADI submitted successfully.";
            $fetch['count'] = $count + 1;
            logStudentTadiSubmit($dbConn, (int)$STUDID, null);
        } else {
            throw new Exception("Insert failed: " . $stmt->error);
        }

        $stmt->close();
    } catch (Exception $e) {
        $fetch['message'] = "Server error: " . $e->getMessage();
        logStudentTadiSubmit($dbConn, (int)$STUDID, $fetch['message']);
    } finally {
        $dbConn->close();
    }
}else {
    $fetch['message'] = "Invalid request method or missing session.";
    logStudentTadiSubmit($dbConn, 0, $fetch['message']);
}


echo json_encode($fetch);
