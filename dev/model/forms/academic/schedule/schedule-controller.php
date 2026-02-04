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
if(!(isset($_SESSION['STUDENT']['ID']) || isset($_SESSION['EMPLOYEE']['ID']))){
    die("Unauthorized access!");
}
include_once 'schedule-functions.php';

// ✅ Safe input parsing
// $id = isset($_SESSION['USERID']) ? intval($_SESSION['USERID']) : 0;

$typeid = isset($_POST['typeid']) ? strtoupper($_POST['typeid']) : '';
$typeid = $typeid === 'INSTRUCTOR' ? 'EMPLOYEE' : $typeid;
$id = isset($_SESSION[$typeid]['ID']) ? intval($_SESSION[$typeid]['ID']) : 0;

$levelid = isset($_POST['levelid']) ? $_POST['levelid'] : 0;
$yearid = isset($_POST['yearid']) ? $_POST['yearid'] : 0;
$periodid = isset($_POST['periodid']) ? $_POST['periodid'] : 0;
$courseid = isset($_POST['courseid']) ? $_POST['courseid'] : 0;

// ✅ Define your queries with required parameters list
$queries = [
    "STUDENT" => [
        "LEVEL" => [
            "query" => "SELECT DISTINCT `ass`.`SchlAcadLvl_ID` `ID`,
                                    `lvl`.`SchlAcadLvl_NAME` `NAME`
            
                            FROM `schoolenrollmentassessment` `ass`
            
                            LEFT JOIN `schoolacademiclevel` `lvl`
                            ON `ass`.`SchlAcadLvl_ID` = `lvl`.`SchlAcadLvlSms_ID`
            
                            WHERE `ass`.`SchlStud_ID` = ?
                            ORDER BY `ID` DESC;",
            "data_types" => "i",
            "param" => [$id],
            "required" => ['id']
        ],
        "YEAR" => [
            "query" => "SELECT DISTINCT `ass`.`SchlAcadYr_ID` `ID`,
                                    `yr`.`SchlAcadYr_NAME` `NAME`
            
                            FROM `schoolenrollmentassessment` `ass`
            
                            LEFT JOIN `schoolacademicyear` `yr`
                            ON `ass`.`SchlAcadyr_ID` = `yr`.`SchlAcadyrSms_ID`
            
                            WHERE `ass`.`SchlStud_ID` = ?
                                AND `ass`.`SchlAcadLvl_ID` = ?
                            ORDER BY `ID` DESC;",
            "data_types" => "is",
            "param" => [$id, $levelid],
            "required" => ['id', 'levelid']
        ],
        "PERIOD" => [
            "query" => "SELECT DISTINCT `ass`.`SchlAcadprd_ID` `ID`,
                                    `prd`.`SchlAcadprd_NAME` `NAME`
            
                            FROM `schoolenrollmentassessment` `ass`
            
                            LEFT JOIN `schoolacademicperiod` `prd`
                            ON `ass`.`SchlAcadprd_ID` = `prd`.`SchlAcadprdSms_ID`
            
                            WHERE `ass`.`SchlStud_ID` = ?
                                AND `ass`.`SchlAcadLvl_ID` = ?
                                AND `ass`.`SchlAcadyr_ID` = ?
                            ORDER BY `ID` DESC;",
            "data_types" => "iss",
            "param" => [$id, $levelid, $yearid],
            "required" => ['id', 'levelid', 'yearid']
        ],
        "COURSE" => [
            "query" => "SELECT DISTINCT `ass`.`SchlAcadcrse_ID` `ID`,
                                    `crse`.`SchlAcadcrses_NAME` `NAME`
                                    
                            FROM `schoolenrollmentassessment` `ass`
            
                            LEFT JOIN `schoolacademiccourses` `crse`
                            ON `ass`.`SchlAcadcrse_ID` = `crse`.`SchlAcadcrseSms_ID`
            
                            WHERE `ass`.`SchlStud_ID` = ?
                                AND `ass`.`SchlAcadLvl_ID` = ?
                                AND `ass`.`SchlAcadyr_ID` = ?
                                AND `ass`.`SchlAcadprd_ID` = ?

                            ORDER BY `NAME` ASC; ",
            "data_types" => "isss",
            "param" => [$id, $levelid, $yearid, $periodid],
            "required" => ['id', 'levelid', 'yearid', 'periodid']
        ],
        "SCHEDULE" => [
            "query" => "SELECT sub.`SchlAcadSubj_CODE` `CODE`,
                            sub.`SchlAcadSubj_DESC` `DESC`,
                            FORMAT_SCHEDULE_STRING(off.`SchlEnrollSubjOff_SCHEDULE_2`) `SCHEDULE`,
                            CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`) `INSTRUCTOR`

                        FROM `schoolenrollmentassessment` ass

                        LEFT JOIN `schoolenrollmentsubjectoffered` off
                        ON FIND_IN_SET(off.`SchlEnrollSubjOffSms_ID`, ass.`SchlAcadSubj_ID`)
                        LEFT JOIN `schoolemployee` emp
                        ON off.`SchlProf_ID` = emp.`SchlEmpSms_ID`
                        LEFT JOIN `schoolacademicsubject` sub
                        ON off.`SchlAcadSubj_ID` = sub.`SchlAcadSubjSms_ID`

                        WHERE ass.`SchlEnrollAss_STATUS` = 1
                        AND ass.`SchlEnrollWithdrawType_ID` = 0
                        AND ass.`SchlStud_ID` = ?
                        AND ass.`SchlAcadLvl_ID` = ?
                        AND ass.`SchlAcadYr_ID` = ?
                        AND ass.`SchlAcadPrd_ID` = ?
                        AND ass.`SchlAcadCrse_ID` = ?",
            "data_types" => "iiiii",
            "param" => [$id, $levelid, $yearid, $periodid, $courseid],
            "required" => ['id', 'levelid', 'yearid', 'periodid', 'courseid']
        ],
    ],
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
        "COURSE" => [
            "query" => "SELECT DISTINCT off.`SchlAcadcrses_ID` `ID`,
                                    `crse`.`SchlAcadcrses_NAME` `NAME`
                                    
                            FROM `schoolenrollmentsubjectoffered` off
            
                            LEFT JOIN `schoolacademiccourses` `crse`
                            ON off.`SchlAcadcrses_ID` = `crse`.`SchlAcadcrseSms_ID`
            
                            WHERE off.`SchlProf_ID` = ?
                                AND off.`SchlAcadLvl_ID` = ?
                                AND off.`SchlAcadyr_ID` = ?
                                AND off.`SchlAcadprd_ID` = ?

                            ORDER BY `NAME` ASC; ",
            "data_types" => "isss",
            "param" => [$id, $levelid, $yearid, $periodid],
            "required" => ['id', 'levelid', 'yearid', 'periodid']
        ],
        "SCHEDULE" => [
            "query" => "SELECT sub.`SchlAcadSubj_CODE` `CODE`,
                            sub.`SchlAcadSubj_DESC` `DESC`,
                            FORMAT_SCHEDULE_STRING(off.`SchlEnrollSubjOff_SCHEDULE_2`) `SCHEDULE`,
                            sec.`SchlAcadSec_CODE` `INSTRUCTOR`

                        FROM `schoolenrollmentsubjectoffered` off

                        LEFT JOIN `schoolacademicsection` sec
                        ON off.`SchlAcadSec_ID` = sec.`SchlAcadSecSms_ID`
                        LEFT JOIN `schoolacademicsubject` sub
                        ON off.`SchlAcadSubj_ID` = sub.`SchlAcadSubjSms_ID`

                        WHERE off.`SchlProf_ID` = ?
                        AND off.`SchlAcadLvl_ID` = ?
                        AND off.`SchlAcadYr_ID` = ?
                        AND off.`SchlAcadPrd_ID` = ?
                        AND off.`SchlAcadCrses_ID` = ?",
            "data_types" => "iiiii",
            "param" => [$id, $levelid, $yearid, $periodid, $courseid],
            "required" => ['id', 'levelid', 'yearid', 'periodid', 'courseid']
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