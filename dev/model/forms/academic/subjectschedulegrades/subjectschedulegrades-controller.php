<?php
/**
 * Builds HTML <option> tags for a <select> element using the same $data format as the table.
 * Uses the first column as value and second column as label.
 *
 * @param array $data The query result (array of associative arrays)
 * @return string The generated <option> tags
 */
function buildDropdown(array $data): string {
    if (empty($data)) {
        return '<option value="">NONE</option>';
    }

    // Get first two column names
    $columns = array_keys($data[0]);
    $valueKey = $columns[0];
    $labelKey = $columns[1];

    $html = '';
    foreach ($data as $row) {
        $value = htmlspecialchars($row[$valueKey]);
        $label = htmlspecialchars($row[$labelKey]);
        $html .= "<option value='$value'>$label</option>";
    }

    return $html;
}

// ✅ Secure cookie flags (must be set before session_start)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');

// PHP error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
require_once '../../../configuration/connection-config.php';

// Check if user is logged in, or if userid is in session
if(!isset($_SESSION['STUDENT']['ID'])){
    die("Unauthorized access!");
}


// ✅ Safe input parsing
$id = $_SESSION['STUDENT']['ID'];
$levelid = isset($_POST['levelid']) ? $_POST['levelid'] : 0;
$yearid = isset($_POST['yearid']) ? $_POST['yearid'] : 0;
$periodid = isset($_POST['periodid']) ? $_POST['periodid'] : 0;
$courseid = isset($_POST['courseid']) ? $_POST['courseid'] : 0;

// ✅ Define your queries with required parameters list
$queries = [
    "LEVEL" => [
        "query" => "SELECT DISTINCT
                    `ENCRYPT_DATA`(lvl.`SchlAcadLvlSms_ID`) `ID`,
                    lvl.`SchlAcadLvl_NAME` `NAME`

                    FROM `schoolenrollmentassessment` ass

                    LEFT JOIN `schoolacademiclevel` lvl
                    ON ass.`SchlAcadLvl_ID` = lvl.`SchlAcadLvlSms_ID`

                    WHERE ass.`SchlEnrollAss_STATUS` = 1
                    AND ass.`SchlEnrollWithdrawType_ID` = 0
                    AND ass.`SchlStud_ID` = ?

                    ORDER BY lvl.`SchlAcadLvlSms_ID` DESC;",
        "data_types" => "i",
        "param" => [$id],
        "required" => ['id']
    ],
    "YEAR" => [
        "query" => "SELECT DISTINCT
                    `ENCRYPT_DATA`(yr.`SchlAcadYrSms_ID`) `ID`,
                    yr.`SchlAcadYr_NAME` `NAME`

                    FROM `schoolenrollmentassessment` ass

                    LEFT JOIN `schoolacademicyear` yr
                    ON ass.`SchlAcadYr_ID` = yr.`SchlAcadYrSms_ID`

                    WHERE ass.`SchlEnrollAss_STATUS` = 1
                    AND ass.`SchlEnrollWithdrawType_ID` = 0
                    AND ass.`SchlStud_ID` = ?
                    AND ass.`SchlAcadLvl_ID` = `DECRYPT_DATA`(?)

                    ORDER BY yr.`SchlAcadYrSms_ID` DESC;",
        "data_types" => "is",
        "param" => [$id, $levelid],
        "required" => ['id', 'levelid']
    ],
    "PERIOD" => [
        "query" => "SELECT DISTINCT
                    `ENCRYPT_DATA`(prd.`SchlAcadPrdSms_ID`) `ID`,
                    prd.`SchlAcadPrd_NAME` `NAME`

                    FROM `schoolenrollmentassessment` ass

                    LEFT JOIN `schoolacademicperiod` prd
                    ON ass.`SchlAcadPrd_ID` = prd.`SchlAcadPrdSms_ID`

                    WHERE ass.`SchlEnrollAss_STATUS` = 1
                    AND ass.`SchlEnrollWithdrawType_ID` = 0
                    AND ass.`SchlStud_ID` = ?
                    AND ass.`SchlAcadLvl_ID` = `DECRYPT_DATA`(?)
                    AND ass.`SchlAcadYr_ID` = `DECRYPT_DATA`(?)

                    ORDER BY prd.`SchlAcadPrdSms_ID` DESC;",
        "data_types" => "iss",
        "param" => [$id, $levelid, $yearid],
        "required" => ['id', 'levelid', 'yearid']
    ],
    "COURSE" => [
        "query" => "SELECT DISTINCT
                    `ENCRYPT_DATA`(crse.`SchlAcadCrseSms_ID`) `ID`,
                    crse.`SchlAcadCrses_NAME` `NAME`

                    FROM `schoolenrollmentassessment` ass

                    LEFT JOIN `schoolacademiccourses` crse
                    ON ass.`SchlAcadCrse_ID` = crse.`SchlAcadCrseSms_ID`

                    WHERE ass.`SchlEnrollAss_STATUS` = 1
                    AND ass.`SchlEnrollWithdrawType_ID` = 0
                    AND ass.`SchlStud_ID` = ?
                    AND ass.`SchlAcadLvl_ID` = `DECRYPT_DATA`(?)
                    AND ass.`SchlAcadYr_ID` = `DECRYPT_DATA`(?)
                    AND ass.`SchlAcadPrd_ID` = `DECRYPT_DATA`(?)

                    ORDER BY `NAME` ASC; ",
        "data_types" => "isss",
        "param" => [$id, $levelid, $yearid, $periodid],
        "required" => ['id', 'levelid', 'yearid', 'periodid']
    ],
    "DISPLAY" => [
        "query" => "CALL `spDisplayStudentGrades`(?,
                    `DECRYPT_DATA`(?),
                    `DECRYPT_DATA`(?),
                    `DECRYPT_DATA`(?),
                    `DECRYPT_DATA`(?),
                    0,0,0);",
        "data_types" => "issss",
        "param" => [$id, $levelid, $yearid, $periodid, $courseid],
        "required" => ['id', 'levelid', 'yearid', 'periodid', 'courseid']
    ]
];

// ✅ Get and sanitize the type
$type = isset($_POST['type']) ? strtoupper(trim($_POST['type'])) : '';
if (!array_key_exists($type, $queries)) {
    die('Error: Invalid parameter.');
}

// ✅ Validate required params for this type dynamically
$required = $queries[$type]['required'];
foreach ($required as $paramName) {
    if (!isset($$paramName) || $$paramName === 0) {
        die("Error: Missing or invalid '$paramName'.");
    }
}

// ✅ Ready to prepare and execute your statement here...
$queryConfig = $queries[$type];
$sql = $queryConfig['query'];
$types = $queryConfig['data_types'];
$params = $queryConfig['param'];

// Prepare the statement
$stmt = $dbConn->prepare($sql);

if (!$stmt) {
    die('Prepare failed: ' . $dbConn->error);
}

// Bind parameters dynamically (if any)
if (!empty($params)) {
    // The spread operator works because bind_param expects separate arguments
    $stmt->bind_param($types, ...$params);
}

try {
    // Execute
    $stmt->execute();
    
    // Get result (if SELECT)
    $result = $stmt->get_result();
    $data = [];
    if ($result) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
    }

    // Close
    $stmt->close();

    // ✅ Now you can output $data as JSON or use it as needed
    if($type === 'DISPLAY'){
        echo json_encode($data);
    }

    if($type !== 'DISPLAY'){
        echo buildDropdown($data);
    }
} catch (Throwable $e) {
    // Don't echo raw error to the browser
    // You can log it instead: error_log($e->getMessage());
    echo 'An error occurred. Please try again.';
}

?>