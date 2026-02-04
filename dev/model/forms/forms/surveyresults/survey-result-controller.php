<?php
// ✅ Secure cookie flags (must be set before session_start)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');

// PHP error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
require_once '../../../configuration/connection-config.php';
require_once '../../../partials/dropdown.php';

// Check if user is logged in, or if userid is in session
if(!isset($_SESSION['EMPLOYEE']['ID'])){
    die("Unauthorized access!");
}

// ✅ Safe input parsing
$typeid = 'EMPLOYEE';
$typeid = $typeid === 'INSTRUCTOR' ? 'EMPLOYEE' : $typeid;
$id = isset($_SESSION[$typeid]['ID']) ? intval($_SESSION[$typeid]['ID']) : 0;

$levelid = isset($_POST['levelid']) ? $_POST['levelid'] : 0;
$yearid = isset($_POST['yearid']) ? $_POST['yearid'] : 0;
$periodid = isset($_POST['periodid']) ? $_POST['periodid'] : 0;

// ✅ Define your queries with required parameters list
$queries = [
    "EMPLOYEE" => [
        "LEVEL" => [
            "query" => "SELECT DISTINCT off.`SchlAcadLvl_ID` `ID`,
                            `lvl`.`SchlAcadLvl_NAME` `NAME`

                        FROM `schoolenrollmentsubjectoffered` off

                        LEFT JOIN `schoolacademiclevel` `lvl`
                        ON off.`SchlAcadLvl_ID` = `lvl`.`SchlAcadLvlSms_ID`

                        WHERE off.`SchlProf_ID` = ?
                        ORDER BY `ID` DESC;",
            "data_types" => "i",
            "param" => [$id],
            "required" => ['id']
        ],
        "YEAR" => [
            "query" => "SELECT DISTINCT off.`SchlAcadYr_ID` `ID`,
                            `yr`.`SchlAcadYr_NAME` `NAME`

                        FROM `schoolenrollmentsubjectoffered` off

                        LEFT JOIN `schoolacademicyear` `yr`
                        ON off.`SchlAcadYr_ID` = `yr`.`SchlAcadYrSms_ID`

                        WHERE off.`SchlProf_ID` = ?
                            AND off.`SchlAcadLvl_ID` = ?
                        ORDER BY `ID` DESC;",
            "data_types" => "is",
            "param" => [$id, $levelid],
            "required" => ['id', 'levelid']
        ],
        "PERIOD" => [
            "query" => "SELECT DISTINCT off.`SchlAcadprd_ID` `ID`,
                            `prd`.`SchlAcadprd_NAME` `NAME`
            
                        
                        FROM `schoolenrollmentsubjectoffered` off
        
                        LEFT JOIN `schoolacademicperiod` `prd`
                        ON off.`SchlAcadprd_ID` = `prd`.`SchlAcadprdSms_ID`
        
                        WHERE off.`SchlProf_ID` = ?
                            AND off.`SchlAcadLvl_ID` = ?
                            AND off.`SchlAcadYr_ID` = ?
                        ORDER BY `ID` DESC;",
            "data_types" => "iss",
            "param" => [$id, $levelid, $yearid],
            "required" => ['id', 'levelid', 'yearid']
        ],
        "SCHEDULE" => [
            "query" => "-- QUERY FOR GETTING SURVEY RESULTS HERE.",
            "data_types" => "iiiii",
            "param" => [$id, $levelid, $yearid, $periodid],
            "required" => ['id', 'levelid', 'yearid', 'periodid']
        ],
    ]
    
];

// ✅ Get and sanitize the type
$type = isset($_POST['type']) ? strtoupper(trim($_POST['type'])) : '';

$queries = $queries[$typeid];

if (array_key_exists($type, $queries)) {
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
        if ($type === 'SCHEDULE') {
            // echo json_encode($data);
            echo generateScheduleTable(json_encode($data));
        } else {
            populateDropdown($data);
        }
    } catch (Throwable $e) {
        // Don't echo raw error to the browser
        // You can log it instead: error_log($e->getMessage());
        exit('An error occurred. Please try again.');
    }
    // die('Error: Invalid parameter.');
}

$dbConn->close();