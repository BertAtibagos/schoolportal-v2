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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type']) && $_POST['type'] === 'SUBMIT_TADI') {
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
        $schltadi_date = $dbConn->real_escape_string($_POST['classDate']);
        $schltadi_timein = $normalizedTimeIn;
        $schltadi_timeout = $normalizedTimeOut;
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
                      AND DATE(schltadi_date) = ?";

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
                                    COUNT(*) as count
                                FROM schooltadi as tadi
                                WHERE tadi.schlprof_id = ?
                                AND tadi.schltadi_isactive = 1
                                AND DATE(tadi.schltadi_date) = ?
                                AND (
                                    (tadi.schltadi_timein <= ? AND tadi.schltadi_timeout >= ?) OR
                                    (tadi.schltadi_timein <= ? AND tadi.schltadi_timeout >= ?)
                                )";

        $overlap_stmt = $dbConn->prepare($overlapTimeSameProf);
        if ($overlap_stmt === false) {
            throw new Exception('Prepare failed: ' . $dbConn->error);
        }

        $overlap_stmt->bind_param(
            'isssss',
            $prof_id,
            $schltadi_date,
            $schltadi_timein,
            $schltadi_timein,
            $schltadi_timeout,
            $schltadi_timeout
        );

        $overlap_stmt->execute();
        $overlap_stmt->bind_result($overlapCount);
        $overlap_stmt->fetch();
        $overlap_stmt->close();

        if ((int)$overlapCount > 0) {
            $fetch['message'] = "A TADI with an overlapping time range already exists for this instructor.";
            logStudentTadiSubmit($dbConn, (int)$STUDID, $fetch['message']);
            echo json_encode($fetch);
            exit;
        }

        $start_image_path = null;
        $end_image_path = null;
        $start_taken_date = null;
        $end_taken_date = null;
        $start_date_only = null;
        $start_time_only = null;
        $end_date_only = null;
        $end_time_only = null;

        if (isset($_FILES['start_attach'], $_FILES['end_attach'])
            && $_FILES['start_attach']['error'] === UPLOAD_ERR_OK
            && $_FILES['end_attach']['error'] === UPLOAD_ERR_OK) {

            if (empty($_FILES['start_attach']['tmp_name']) || empty($_FILES['end_attach']['tmp_name'])
                || (int)$_FILES['start_attach']['size'] <= 0 || (int)$_FILES['end_attach']['size'] <= 0) {
                $fetch['message'] = "Uploaded file is empty.";
                logStudentTadiSubmit($dbConn, (int)$STUDID, $fetch['message']);
                echo json_encode($fetch);
                exit;
            }

            $prof_id = $_POST['instructor'] ?? 'unknown_prof';
            
            $prof_id = preg_replace('/[^A-Za-z0-9_-]/', '', $prof_id);
            if ($prof_id === '') {
                $prof_id = 'unknown_prof';
            }

            $date_folder = date('Y-m-d');

            $baseDir = __DIR__ . '/../../../../../../public/attachment/';
            $uploadDir = $baseDir . $prof_id . '/' . $date_folder . '/';

            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $fetch['message'] = "Failed to create upload directory.";
                    logStudentTadiSubmit($dbConn, (int)$STUDID, $fetch['message']);
                    echo json_encode($fetch);
                    exit;
                }
            }

            date_default_timezone_set("Asia/Manila");

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $allowedMimeTypes  = ['image/jpeg', 'image/png', 'image/webp'];

            // Reusable helper to validate + move + read EXIF for one file
            $processAttachment = function (array $file, string $prefix) use (
                $allowedExtensions, $allowedMimeTypes, $uploadDir, $prof_id, $date_folder
            ) {
                $originalName = basename($file['name']);
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                if (!in_array($extension, $allowedExtensions, true)) {
                    return [null, null, "Invalid file type for {$prefix} attachment. Only JPG, PNG, and WEBP are allowed."];
                }

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if (!in_array($mimeType, $allowedMimeTypes, true)) {
                    return [null, null, "Invalid file format for {$prefix} attachment."];
                }

                // e.g. S-38_2025-10-01_1759290535.jpg / E-38_2025-10-01_1759290535.jpg
                $uniqueName = $prefix . "-" . $prof_id . "_" . $date_folder . "_" . time() . "." . $extension;
                $targetPath = $uploadDir . $uniqueName;

                if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                    return [null, null, "Failed to upload {$prefix} image."];
                }

                $imagePath = 'attachment/' . $prof_id . '/' . $date_folder . '/' . $uniqueName;
                $takenDate = null;

                if (function_exists('exif_read_data')) {
                    $exif = @exif_read_data($targetPath);
                    if ($exif !== false && isset($exif['DateTimeOriginal'])) {
                        $ts = strtotime($exif['DateTimeOriginal']);
                        if ($ts) {
                            $takenDate = date("Y-m-d H:i:s", $ts);
                        }
                    }
                }

                return [$imagePath, $takenDate, null];
            };

            [$start_image_path, $start_taken_date, $startErr] = $processAttachment($_FILES['start_attach'], 'S');
            if ($startErr) {
                $fetch['message'] = $startErr;
                logStudentTadiSubmit($dbConn, (int)$STUDID, $fetch['message']);
                echo json_encode($fetch);
                exit;
            }

            [$end_image_path, $end_taken_date, $endErr] = $processAttachment($_FILES['end_attach'], 'E');
            if ($endErr) {
                $fetch['message'] = $endErr;
                logStudentTadiSubmit($dbConn, (int)$STUDID, $fetch['message']);
                echo json_encode($fetch);
                exit;
            }
            
            $start_date_only = $start_taken_date ? date("Y-m-d", strtotime($start_taken_date)) : null;
            $start_time_only = $start_taken_date ? date("H:i:s", strtotime($start_taken_date)) : null;

            $end_date_only = $end_taken_date ? date("Y-m-d", strtotime($end_taken_date)) : null;
            $end_time_only = $end_taken_date ? date("H:i:s", strtotime($end_taken_date)) : null;
        }

        if (empty($start_image_path) || empty($end_image_path)) {
            $fetch['message'] = "Both start and end images are required to submit TADI.";
            logStudentTadiSubmit($dbConn, (int)$STUDID, $fetch['message']);
            echo json_encode($fetch);
            exit;
        }

        $stmt = $dbConn->prepare("INSERT INTO schooltadi 
                (schltadi_mode, 
                schltadi_type, 
                schltadi_date, 
                schltadi_timein, 
                schltadi_timeout, 
                schltadi_activity, 
                schltadi_isactive, 
                schltadi_status,
                schlstud_id, 
                schlacadlvl_id, 
                schlacadyr_id,
                schlprof_id, 
                schlenrollsubjoff_id, 
                schlacadprd_id, 
                startschltadi_filepath, 
                starttadi_exifDate,
                starttadi_exifTime,
                endschltadi_filepath, 
                endtadi_exifDate,
                endtadi_exifTime,
                schltadi_late_status,
                schltadi_late_date,
                schltadi_late_reason,
                schltadi_mkup_date)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $isactive = 1;
        $status = 0;
        $isupdated = 0;

        $stmt->bind_param(
            "ssssssiiiiiiiissssssisss",
            $schltadi_mode,
            $schltadi_type,
            $schltadi_date,
            $schltadi_timein,
            $schltadi_timeout,
            $schltadi_activity,
            $isactive,
            $status,
            $STUDID,
            $LVLID,
            $YRID,
            $prof_id,
            $subj_id,
            $PRDID,
            $start_image_path,
            $start_date_only,
            $start_time_only,
            $end_image_path,
            $end_date_only,
            $end_time_only,
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
}


echo json_encode($fetch);
