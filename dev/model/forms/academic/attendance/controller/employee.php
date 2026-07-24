<?php
session_start();
require('../config/conn-config.php');

$type = $_POST['type'];
$fetch = [];

if($type == "FETCH_ATTENDANCE_LOGS"){

    $USERID = $_SESSION['EMPLOYEE']['ID'];
    // $USERID = 1;
    $subjId = $_POST['subjectId'] ?? null;

    $filter = "";
    $value = [$USERID];
    $bind = "i";

    if ($subjId !== "null" && $subjId !== "" && $subjId !== null) {
        $filter .= " AND log_hist.SchlEnrollSubjOff_ID = ?";
        $value[] = $subjId;
        $bind .= "i";
    }

    if (isset($_POST['dateStart']) && isset($_POST['dateEnd'])) {
        $filter .= " AND DATE(log_hist.SchlClsLogHis_DATETIME) BETWEEN ? AND ?";
        $value[] = $_POST['dateStart'];
        $value[] = $_POST['dateEnd'];
        $bind .= "ss";
    }
    
    $qry = "SELECT
                `SchlClsLogHis_ID` AS id,
                DATE(log_hist.SchlClsLogHis_DATETIME) AS log_date,
                log_hist.SchlUserRF_ID,
                MIN(log_hist.SchlClsLogHis_DATETIME) AS first_login,
                MAX(log_hist.SchlClsLogHis_DATETIME) AS last_login
            FROM schoolclassloghistory AS log_hist
            WHERE `SchlEmp_ID` = ?
            $filter
            GROUP BY
                DATE(log_hist.SchlClsLogHis_DATETIME),
                log_hist.SchlUserRF_ID
            ORDER BY log_date DESC";

    $stmt = $dbConn->prepare($qry);

    if (!$stmt) {
        echo json_encode(["error" => $dbConn->error]);
        exit;
    }

    $stmt->bind_param($bind, ...$value);
    $stmt->execute();
    $result = $stmt->get_result();
    $fetch = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $dbConn->close();
}

echo json_encode($fetch);
?>