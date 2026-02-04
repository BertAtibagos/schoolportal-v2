<?php

// function serverToPHTime($serverTime, $serverTZ = 'UTC'){
//     $date = new DateTime($serverTime, new DateTimeZone($serverTZ));
//     $date->setTimezone(new DateTimeZone('Asia/Manila'));
//     return $date->format('Y-m-d H:i:s');
// }

function log_cleaner($data){


}
require_once '../configuration/connection-config.php';

// Check if user is logged in, or if userid is in session
if(!(isset($_SESSION['STUDENT']['ID']) || isset($_SESSION['EMPLOYEE']['ID']))){
    die("Unauthorized access!");
}


$email = isset($_SESSION['EMAIL_ADDRESS']) ? $_SESSION['EMAIL_ADDRESS'] : '';
$email_text = '%"email":"' . $email . '"%';

$student_id = isset($_SESSION['STUDENT']['ID']) ? $_SESSION['STUDENT']['ID'] : -1;
$stud_text = '%"id":"' . $student_id . '"%';

$employee_id = isset($_SESSION['EMPLOYEE']['ID']) ? $_SESSION['EMPLOYEE']['ID'] : -1;
$emp_text = '%"id":"' . $employee_id . '"%';

$qry = "SELECT ylog.`SysHisLog_TRANSDATE` `DATETIME`,
            ylog.`SysHisLog_SYNTAX` `SYNTAX`,
            ylog.`SysHisLog_OPERATION` `OPERATION`,
            ylog.`SysHisLog_MODULENAME` `MODULE_NAME`

        FROM `systemuser` us

        LEFT JOIN `systemhistorylog` ylog
        ON us.`SysUser_ID` = ylog.`SysUser_ID` 
            OR ylog.`SysUser_ID` IN (?, ?) 
            OR ylog.`SysHisLog_SYNTAX` LIKE ? 
            OR ylog.`SysHisLog_SYNTAX` LIKE ? 
            OR ylog.`SysHisLog_SYNTAX` LIKE ? 

        WHERE us.`SysUser_ISACTIVE` = 1
        AND us.`SysUser_STATUS` = 1
        AND us.`SchlUser_ID` = ?

        ORDER BY ylog.`SysHisLog_ID` DESC

        LIMIT 10";

$stmt = $dbConn->prepare($qry);
$stmt->bind_param('iisssi', $student_id, $employee_id, $stud_text, $emp_text, $email_text, $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<tr><td colspan='3'>No Logs Found.</td></tr>";
    exit;
}

// $debugQuery = $qry;
// $debugQuery = str_replace("?", "'%s'", $debugQuery);
// $debugQuery = vsprintf($debugQuery, [$student_id, $employee_id, $stud_text, $emp_text, $email_text, $student_id]);

// echo "<pre>$debugQuery</pre>";

$counter = 1;
while($row = $result->fetch_assoc()) {
    $syntax = json_decode($row["SYNTAX"], true);

    // Skip invalid JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        continue;
    }

    $status = isset($syntax['status']) ? ucwords(strtolower($syntax["status"])) . ": "  : '';
    $email = isset($syntax['email']) ? " - " . $syntax['email'] : '';
    $method = isset($syntax['method']) ? ucwords($syntax['method']) : '';

    $module = isset($row["MODULE_NAME"]) ? ucwords(strtolower($row["MODULE_NAME"])) : '';
    $operation = isset($row["OPERATION"]) ? ucwords(strtolower($row["OPERATION"])) : '';
    $datetime = isset($row["DATETIME"]) ? $row["DATETIME"] : '';

    // <td><strong>{$counter}</strong></td>
    // <td>{$status}{$operation}{$email}</td>
    echo "
    <tr>
        <td>{$datetime}</td>
        <td>{$method} {$module}</td>
        <td>{$status}{$operation}</td>
    </tr>
    ";

    $counter++;
}

