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

// Check if user is logged in, or if userid is in session
if(!(isset($_SESSION['STUDENT']['ID']) || isset($_SESSION['EMPLOYEE']['ID']))){
    die("Unauthorized access!");
}

// ✅ Safe input parsing
if(!isset($_SESSION['STUDENT']['ID']) || isset($_SESSION['EMPLOYEE']['ID'])){
    $typeid = 'EMPLOYEE';
} else {
    $typeid = 'STUDENT';

}

$id = isset($_SESSION[$typeid]['ID']) ? intval($_SESSION[$typeid]['ID']) : 0;

$levelid = isset($_SESSION[$typeid]['LVLID']) ? $_SESSION[$typeid]['LVLID'] : 0;
$yearid = isset($_SESSION[$typeid]['YRID']) ? $_SESSION[$typeid]['YRID'] : 0;
$periodid = isset($_SESSION[$typeid]['PRDID']) ? $_SESSION[$typeid]['PRDID'] : 0;
$yearlevelid = isset($_SESSION[$typeid]['YRLVLID']) ? $_SESSION[$typeid]['YRLVLID'] : 0;
$courseid = isset($_SESSION[$typeid]['CRSEID']) ? $_SESSION[$typeid]['CRSEID'] : 0;
$sectionid = isset($_SESSION[$typeid]['SECID']) ? $_SESSION[$typeid]['SECID'] : 0;

// ✅ Define your queries with required parameters list
$queries = [
    "STUDENT" => [
        "query" => "SELECT stud.`SchlStud_IDNO` `IDNO`,
                        CONCAT(yrlvl.`SchlAcadYrLvl_NAME`, ', ',
                            crse.`SchlAcadCrses_NAME`
                        ) `INFO`,
                        sec.`SchlAcadSec_CODE` `SECTION`,
                        info.`SchlEnrollRegStudInfo_MOB_NO` `MOBILE`,
                        info.`SchlEnrollRegStudInfo_TEL_NO` `TELEPHONE`,
                        DATE_FORMAT(info.`SchlEnrollRegStudInfo_BIRTH_DATE`, '%m-%d-%Y') `BIRTHDATE`,
                        info.`SchlEnrollRegStudInfo_NATIONALITY` `NATIONALITY`,
                        info.`SchlEnrollRegStudInfo_RELIGION` `RELIGION`,
                        CONCAT(
                                IFNULL(info.`SchlEnrollRegStudInfo_PERM_ADD`, ''), ' ',
                                IFNULL(perm_brgy.`PhilAreaLocBrgy_NAME`, ''), ', ',
                                IFNULL(perm_mun.`PhilAreaLocMun_NAME`, ''), ', ',
                                IFNULL(perm_prov.`PhilAreaLocProv_NAME`, ''), ' ',
                                IFNULL(info.`SchlEnrollRegStudInfo_PERM_ZIPCODE`, '')
                            ) `PERMANENT_ADD`,
                        CONCAT(IFNULL(info.`SchlEnrollRegStudInfo_PRES_ADD`, ''), ' ',
                            IFNULL(pres_brgy.`PhilAreaLocBrgy_NAME`, ''), ', ',
                            IFNULL(pres_mun.`PhilAreaLocMun_NAME`, ''), ', ',
                            IFNULL(pres_prov.`PhilAreaLocProv_NAME`, ''), ' ',
                            IFNULL(info.`SchlEnrollRegStudInfo_PRES_ZIPCODE`, '')
                        ) `PRESENT_ADD`

                    FROM `schoolenrollmentassessment` ass

                    LEFT JOIN `schoolstudent` stud
                    ON ass.`SchlStud_ID` = stud.`SchlStudSms_ID`
                    LEFT JOIN `schoolenrollmentregistrationstudentinformation` info
                    ON stud.`SchlEnrollRegColl_ID` = info.`SchlEnrollReg_ID`
                    LEFT JOIN `schoolacademiccourses` crse
                    ON ass.`SchlAcadCrse_ID` = crse.`SchlAcadCrseSms_ID`
                    LEFT JOIN `schoolacademicyearlevel` yrlvl
                    ON ass.`SchlAcadYrLvl_ID` = yrlvl.`SchlAcadYrLvlSms_ID`
                    LEFT JOIN `schoolacademicsection` sec
                    ON ass.`SchlAcadSec_ID` = sec.`SchlAcadSecSms_ID`
                                    
                    LEFT JOIN `philippine_area_location_barangay` perm_brgy
                    ON info.`schlenrollregstudinfo_perm_brgy_id` = perm_brgy.`philarealocbrgy_id`
                    LEFT JOIN `philippine_area_location_barangay` pres_brgy
                    ON info.`schlenrollregstudinfo_pres_brgy_id` = pres_brgy.`philarealocbrgy_id`

                    LEFT JOIN `philippine_area_location_municipality` perm_mun
                    ON info.`schlenrollregstudinfo_perm_mun_id` = perm_mun.`philarealocmun_id`
                    LEFT JOIN `philippine_area_location_municipality` pres_mun
                    ON info.`schlenrollregstudinfo_pres_mun_id` = pres_mun.`philarealocmun_id`

                    LEFT JOIN `philippine_area_location_province` perm_prov
                    ON info.`schlenrollregstudinfo_perm_prov_id` = perm_prov.`philarealocprov_id`
                    LEFT JOIN `philippine_area_location_province` pres_prov
                    ON info.`schlenrollregstudinfo_pres_prov_id` = pres_prov.`philarealocprov_id`

                    LEFT JOIN `philippine_area_location_country` perm_count
                    ON info.`SchlEnrollRegStudInfo_PERM_CTRY_ID` = perm_count.`PhilAreaLocCtry_ID`
                    LEFT JOIN `philippine_area_location_country` pres_count
                    ON info.`SchlEnrollRegStudInfo_PRES_CTRY_ID` = pres_count.`PhilAreaLocCtry_ID`

                    WHERE ass.`SchlEnrollWithdrawType_ID` = 0
                    AND ass.`SchlEnrollAss_STATUS` = 1
                    AND crse.`SchlAcadCrses_STATUS` = 1
                    AND crse.`SchlAcadCrses_ISACTIVE` = 1
                    AND yrlvl.`SchlAcadYrLvl_STATUS` = 1
                    AND yrlvl.`SchlAcadYrLvl_ISACTIVE` = 1
                    AND sec.`SchlAcadSec_STATUS` = 1
                    AND sec.`SchlAcadSec_ISACTIVE` = 1
                    AND ass.`SchlStud_ID` = ?
                    AND ass.`SchlAcadLvl_ID` = ?
                    AND ass.`SchlAcadYr_ID` = ?
                    AND ass.`SchlAcadPrd_ID` = ?
                    AND ass.`SchlAcadYrLvl_ID` = ?
                    AND ass.`SchlAcadCrse_ID` = ?
                    AND ass.`SchlAcadSec_ID` = ?",
        "data_types" => "iiiiiii",
        "param" => [$id, $levelid, $yearid, $periodid, $yearlevelid, $courseid, $sectionid],
        "required" => ['id', 'levelid', 'yearid', 'periodid', 'yearlevelid', 'courseid', 'sectionid']
    ],
    "EMPLOYEE" =>[
        "query" => "SELECT emp.`SchlEmp_IDNO` `IDNO`,
                        dept.`SchlDept_NAME` `INFO`,
                        pos.`SchlJobPos_NAME` `SECTION`,
                        emp.`SchlEmp_MOBNO` `MOBILE`,
                        '' `TELEPHONE`,
                        DATE_FORMAT(emp.`SchlEmp_BDAY`, '%m-%d-%Y') `BIRTHDATE`,
                        emp.`SchlEmp_CITIZENSHIP` `NATIONALITY`,
                        emp.`SchlEmp_RELIGION` `RELIGION`,
                        emp.`SchlEmp_PERMANENTADDRESS` `PERMANENT_ADD`,
                        emp.`SchlEmp_PRESENTADDRESS` `PRESENT_ADD`

                    FROM `schoolemployee` emp

                    LEFT JOIN `schooljobposition` pos
                    ON emp.`SchlJobPos_ID` = pos.`SchlJobPosSms_ID`
                    LEFT JOIN `schooldepartment` dept
                    ON emp.`SchlDept_ID` = dept.`SchlDeptSms_ID`

                    WHERE emp.`SchlEmp_STATUS` = 1
                    AND emp.`SchlEmp_ISACTIVE` = 1
                    AND pos.`SchlJobPos_ISACTIVE` = 1
                    AND pos.`SchlJobPos_STATUS` = 1
                    AND dept.`SchlDept_ISACTIVE` = 1
                    AND dept.`SchlDept_STATUS` = 1
                    AND emp.`SchlEmpSms_ID` = ?",
        "data_types" => "i",
        "param" => [$id],
        "required" => ['id']
    ]
];

// ✅ Get and sanitize the type
$type = $typeid;

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

        echo json_encode($data);

    } catch (Throwable $e) {
        // Don't echo raw error to the browser
        // You can log it instead: error_log($e->getMessage());
        exit('An error occurred. Please try again.');
    }
    // die('Error: Invalid parameter.');
}


$dbConn->close();