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

if ($_POST['type'] == 'UPDATE_TADI_STATUS') {
    if (!$dbConn) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }

    if (empty($_SESSION['EMPLOYEE']['ID'])) {
        echo json_encode([
            'success' => false, 
            'error' => 'Your session has expired. Please log in again',
            'message' => 'Your session has expired. Please log in again.'
        ]);
        exit;
    }

    $USERID = $_SESSION['EMPLOYEE']['ID'];
    $status = $_POST['tadi_status'];
    $tadi_id = $_POST['tadi_ID'];
    
    if ($status == 0) {
        $status = 1;
    } else {
        $status = 0;
    }
    
    $query = "UPDATE schooltadi SET schltadi_status = $status WHERE schltadi_id = $tadi_id";
    $result = $dbConn->query($query);
    
    if (!$result) {
        echo json_encode(['success' => false, 'error' => 'Query failed: ' . $dbConn->error]);
        exit;
    }
    
    echo json_encode(['success' => true, 'status' => $status]);
}

if($_POST['type'] == 'UPLOAD_IMAGE_PROF'){
     header('Content-Type: application/json');

    $USERID = $_SESSION['EMPLOYEE']['ID'];
    $REC_ID = $_POST['tadi_id'];
    $fetch =[];

    $image_path = null;
    error_log(print_r($_FILES, true));

    if (isset($_FILES['attach']) && $_FILES['attach']['error'] === UPLOAD_ERR_OK) {
        
        $prof_id = $USERID;
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
        $extension = explode('.', $originalName);
        $uniqueName = $prof_id. "_" . $date_folder . "_" . time() . "." . end($extension);
        $targetPath = $uploadDir . $uniqueName;

        if (move_uploaded_file($_FILES['attach']['tmp_name'], $targetPath)) {
            $image_path = 'attachment/' . $prof_id . '/' . $date_folder . '/' . $uniqueName;
        } else {
            $fetch['message'] = "Failed to upload image.";
            echo json_encode($fetch);
            exit;
        }
    }

    $stmt = $dbConn->prepare("UPDATE schooltadi SET schltadi_filepath = ? WHERE schltadi_id = ? AND schlprof_id = ?");

    $stmt->bind_param("sii",$image_path, $REC_ID, $USERID);

    if ($stmt->execute()) {
        $fetch['success'] = true;
        $fetch['message'] = "Image Uploaded Successfuly.";
    } else {
        $fetch['success'] = false;
        $fetch['message'] = "Update failed: " . $stmt->error;
    }

    

    $stmt->close();
    echo json_encode($fetch);

}
if($_POST['type'] == 'GET_UNVERIFIED_COUNT'){
        $subj_off = $_POST['sub_off_id'];
        
        $qry = "SELECT COUNT(`schltadi_id`) AS unverified_count
                FROM `schooltadi` 
                WHERE `schltadi_status` = 0 
                AND`schlenrollsubjoff_id` = ?";

        $stmt = $dbConn->prepare($qry);
        $stmt->bind_param("i", $subj_off);
        $stmt->execute();
        $result = $stmt->get_result();
        $fetch = $result->fetch_assoc();
        $stmt->close();
        $dbConn->close();
        echo json_encode($fetch);
}
?>