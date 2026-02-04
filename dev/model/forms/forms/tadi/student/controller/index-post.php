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

$fetch = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type']) && $_POST['type'] === 'SUBMIT_TADI') {
    $STUDID = $_SESSION['STUDENT']['ID'] ?? 0;
    $LVLID = $_SESSION['STUDENT']['LVLID'] ?? 0;
    $YRID = $_SESSION['STUDENT']['YRID'] ?? 0;
    $PRDID = $_SESSION['STUDENT']['PRDID'] ?? 0;


    // SESSION validation
    if (!$STUDID || !$LVLID || !$YRID || !$PRDID) {
        $fetch['message'] = "Invalid session. Please log in again.";
        echo json_encode($fetch);
        exit;
    }

    try {
        $prof_id = $dbConn->real_escape_string($_POST['instructor']);
        $schltadi_mode = $dbConn->real_escape_string($_POST['learning_delivery_modalities']);
        $schltadi_type = $dbConn->real_escape_string($_POST['session_type']);
        $schltadi_date = date('Y-m-d');
        $schltadi_timein = date('H:i:s', strtotime($_POST['classStartDateTime']));
        $schltadi_timeout = date('H:i:s', strtotime($_POST['classEndDateTime']));
        $schltadi_activity = $dbConn->real_escape_string($_POST['comments']);
        $subj_id = $dbConn->real_escape_string($_POST['subjoff_id']);

        $schltadi_late_status = isset($_POST['late_class_date']) && !empty($_POST['late_class_date']) ? 1 : 0;
        $schltadi_late_date = $schltadi_late_status ? $dbConn->real_escape_string($_POST['late_class_date']) : null;
        $schltadi_late_reason = $schltadi_late_status && isset($_POST['late_reason']) ? $dbConn->real_escape_string($_POST['late_reason']) : null;



        // TADI limit check
        $check_sql = "SELECT COUNT(*) as count 
                      FROM schooltadi 
                      WHERE schlenrollsubjoff_id = $subj_id 
                      AND schlprof_id = $prof_id 
                      AND DATE(schltadi_date) = '$schltadi_date'";

        $result = $dbConn->query($check_sql);
        $row = $result->fetch_assoc();
        $count = (int)$row['count'];

        if ($count >= 3) {
            $fetch['message'] = "You have already submitted 3 TADIs today.";
            echo json_encode($fetch);
            exit;
        }

        // Time overlap check
        $overlap_sql = "SELECT COUNT(*) as count 
                        FROM schooltadi 
                        WHERE schlenrollsubjoff_id = $subj_id 
                        AND schlprof_id = $prof_id 
                        AND DATE(schltadi_date) = '$schltadi_date'
                        AND (
                            (schltadi_timein <= '$schltadi_timein' AND schltadi_timeout >= '$schltadi_timein') OR
                            (schltadi_timein <= '$schltadi_timeout' AND schltadi_timeout >= '$schltadi_timeout')
                        )";

        $overlap_result = $dbConn->query($overlap_sql);
        $overlap_row = $overlap_result->fetch_assoc();

        if ((int)$overlap_row['count'] > 0) {
            $fetch['message'] = "Submission time overlaps with a previous entry.";
            echo json_encode($fetch);
            exit;
        }

        // image upload
        $image_path = null;
        $taken_date = null; // for storing original taken date from EXIF
        error_log(print_r($_FILES, true));

        if (isset($_FILES['attach']) && $_FILES['attach']['error'] === UPLOAD_ERR_OK) {
            $prof_id = $_POST['instructor'] ?? 'unknown_prof';
            $date_folder = date('Y-m-d');

            $baseDir = '../../attachment/';
            $uploadDir = $baseDir . $prof_id . '/' . $date_folder . '/';

            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    $fetch['message'] = "Failed to create upload directory.";
                    echo json_encode($fetch);
                    exit;
                }
            }

            date_default_timezone_set("Asia/Manila");
            $originalName = basename($_FILES['attach']['name']);
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($extension, $allowedExtensions)) {
                $fetch['message'] = "Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.";
                echo json_encode($fetch);
                exit;
            }

        
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['attach']['tmp_name']);
            finfo_close($finfo);

            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($mimeType, $allowedMimeTypes)) {
                $fetch['message'] = "Invalid file format.";
                echo json_encode($fetch);
                exit;
            }

            
            $uniqueName = $prof_id . "_" . $date_folder . "_" . time() . "." . $extension;
            $targetPath = $uploadDir . $uniqueName;

            if (move_uploaded_file($_FILES['attach']['tmp_name'], $targetPath)) {
                $image_path = 'attachment/' . $prof_id . '/' . $date_folder . '/' . $uniqueName;

                
                if (function_exists('exif_read_data')) {
                    $exif = @exif_read_data($targetPath);
                    if ($exif !== false && isset($exif['DateTimeOriginal'])) {
                        $taken_date = date("Y-m-d H:i:s", strtotime($exif['DateTimeOriginal']));
                        if ($taken_date) {
                            $exif_date_only = date("Y-m-d", strtotime($taken_date));
                            $exif_time_only = date("H:i:s", strtotime($taken_date));
                        } else {
                            $exif_date_only = null;
                            $exif_time_only = null;
                        }
                    } else {
                        $taken_date = null;
                    }
                }
            } else {
                $fetch['message'] = "Failed to upload image.";
                echo json_encode($fetch);
                exit;
            }
        }

        if (empty($image_path)) {
            $fetch['message'] = "Image is required to submit TADI.";
            echo json_encode($fetch);
            exit;
        }


        // Insert
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
                schltadi_filepath, 
                tadi_exifDate,
                tadi_exifTime,
                schltadi_late_status,
                schltadi_late_date,
                schltadi_late_reason)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $isactive = 1;
        $status = 0;
        $isupdated = 0;

        $stmt->bind_param(
            "ssssssiiiiiiiisssiss",
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
            $image_path,
            $exif_date_only,
            $exif_time_only,
            $schltadi_late_status,
            $schltadi_late_date,
            $schltadi_late_reason

        );

        if ($stmt->execute()) {
            $fetch['success'] = true;
            $fetch['message'] = "TADI submitted successfully.";
            $fetch['count'] = $count + 1;
        } else {
            throw new Exception("Insert failed: " . $stmt->error);
        }

        $stmt->close();
    } catch (Exception $e) {
        $fetch['message'] = "Server error: " . $e->getMessage();
    } finally {
        $dbConn->close();
    }
}


echo json_encode($fetch);
