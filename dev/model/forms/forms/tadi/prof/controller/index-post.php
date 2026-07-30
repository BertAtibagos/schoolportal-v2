<?php
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.cookie_samesite', 'Strict');

    ini_set('display_errors', 0);
    ini_set('log_errors', 1);

    session_start();
    include('../../../../../configuration/connection-config.php');

$fetch = [];
$type = $_POST['type'] ?? '';
$queryType = ['UPDATE_TADI_STATUS', 'GET_UNVERIFIED_COUNT'];

if($_SESSION['EMPLOYEE'] && in_array($type, $queryType, true)){
    switch($type){
        case 'UPDATE_TADI_STATUS':
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

            if (!isset($_POST['tadi_status'], $_POST['tadi_ID'], $_POST['sub_off_id'])) {
                echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
                exit;
            }

            $USERID = $_SESSION['EMPLOYEE']['ID'];
            $status = (int) $_POST['tadi_status'];
            $tadi_id = (int) $_POST['tadi_ID'];
            $subject_off_id = (int) $_POST['sub_off_id'];
            
            if ($status == 0) {
                $status = 1;
            } else {
                $status = 0;
            }

            $dueDateQuery = "SELECT t.schltadi_date
                  FROM schooltadi t
                  WHERE t.schlprof_id = ?
                  AND t.schltadi_id = ?
                  AND t.schlenrollsubjoff_id = ?
                  AND t.schltadi_isconfirm = 0
                  AND t.schltadi_status = 0
                  AND t.schltadi_isactive = 1";

            $dueStmt = $dbConn->prepare($dueDateQuery);
            if (!$dueStmt) {
                echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $dbConn->error]);
                exit;
            }
            $dueStmt->bind_param("iii", $USERID, $tadi_id, $subject_off_id);
            $dueStmt->execute();
            $dueResult = $dueStmt->get_result();
            $dueRow = $dueResult->fetch_assoc();
            $dueStmt->close();

            if (!$dueRow) {
                echo json_encode(['success' => false, 'error' => 'Record not found or already processed']);
                exit;
            }
            
            $recordDate = new DateTime($dueRow['schltadi_date']);
            $today = new DateTime();
            $today->setTime(0, 0, 0);
            $recordDate->setTime(0, 0, 0);

            $pastLimit = clone $today;
            $pastLimit->modify('-3 days');

            if ($recordDate < $pastLimit) {
                echo json_encode(['success' => false, 'error' => 'This record is past the 3-day verification window and can no longer be updated']);
                exit;
            }
            
            $query = "UPDATE schooltadi
                    SET schltadi_status = ?,
                        tadi_verified_date = NOW()
                    WHERE schltadi_id = ?
                    AND schlprof_id = ?
                    AND schlenrollsubjoff_id = ?
                    AND schltadi_isactive = 1
                    AND schltadi_isconfirm = 0
                    AND schltadi_status = 0";
            $stmt = $dbConn->prepare($query);
            if (!$stmt) {
                echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $dbConn->error]);
                exit;
            }
            $stmt->bind_param("iiii", $status, $tadi_id, $USERID, $subject_off_id);
            if (!$stmt->execute()) {
                echo json_encode(['success' => false, 'error' => 'Query failed: ' . $stmt->error]);
                exit;
            }

            $affectedRows = $stmt->affected_rows;

            $stmt->close();
            $dbConn->close();

            if ($affectedRows === 0) {
                echo json_encode(['success' => false, 'error' => 'Unauthorized or invalid record state']);
                exit;
            }
            
            echo json_encode(['success' => true, 'status' => $status]);
            break;

        case 'GET_UNVERIFIED_COUNT':
            if (!isset($_POST['sub_off_id'])) {
                echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
                exit;
            }

            $subj_off = (int) $_POST['sub_off_id'];
                
                $qry = "SELECT COUNT(`schltadi_id`) AS unverified_count
                        FROM `schooltadi` 
                        WHERE `schltadi_status` = 0
                        AND schltadi_isactive = 1
                        AND `schlenrollsubjoff_id` = ?";

                $stmt = $dbConn->prepare($qry);
                $stmt->bind_param("i", $subj_off);
                $stmt->execute();
                $result = $stmt->get_result();
                $fetch = $result->fetch_assoc();
                $stmt->close();
                $dbConn->close();
                echo json_encode($fetch);
            break;
        default:
            http_response_code(400);
            echo json_encode(["error" => "Invalid request type."]);
            exit;
    }
}
?>