<?php
// ✅ Secure cookie flags (must be set before session_start)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');

// PHP error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
require_once '../../../../configuration/connection-config.php';
$_SESSION['role'] = 'admin';

// Check if session exists and validate user role
if (!isset($_SESSION['USERID']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

function buildTableBody(mysqli_result $result): string
{
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($rows)) {
        return '<tr><td colspan="4" class="text-center text-danger" style="font-weight: 500">No matching record found</td></tr>';
    }

    $statusPriority = [
        'ACCOUNT' => 'unregistered',
        'SUSPENDED' => 'suspended',
        'DELETED' => 'deleted'
    ];

    $html = '';
    foreach ($rows as $row) {
        // Determine user status
        $statusText = '';
        foreach ($statusPriority as $key => $label) {
            if (intval($row[$key]) === 0) {
                $statusText = $label;
                break;
            }
        }

        // Build status badge if applicable
        $span = '';
        if ($statusText !== '') {
            $span = '<span class="text-bg-secondary bg-opacity-50 rounded-pill px-2 ms-2 user-select-none" style="font-size: 10px; padding-block: 2px;">' . $statusText . '</span>';
        }

        // Start row
        $html .= '<tr>';
        $html .= '<td class="ps-4"><div class="d-flex align-items-center">' . htmlspecialchars($row['NO']) . '</div></td>';
        $html .= '<td><div class="d-flex align-items-center">' . ucwords(strtolower(htmlspecialchars($row['NAME']))) . $span . '</div></td>';

        // Start action column
        $html .= '<td class="text-end pe-4">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle border-0 p-0" style="color: #071976" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                <i class="fa-solid fa-gear"></i>
                            </button>
                            <ul class="dropdown-menu" id="' . $row['ID'] . '">';

        // Logic for account actions
        if (intval($row['ACCOUNT']) > 0) {
            $html .= '<li><button class="dropdown-item rounded-0 btnAction" id="edituser" value="edituser"><i class="pe-2 fa-solid fa-pen-to-square"></i> Edit </button></li>';

            if (intval($row['SUSPENDED']) == 1 && intval($row['ACCOUNT']) == 0) {
                $html .= '<li><button class="dropdown-item rounded-0 btnAction" id="suspenduser" value="suspenduser"><i class="pe-2 fa-solid fa-ban text-secondary"></i> Suspend user </button></li>';
            } else {
                $html .= '<li><button class="dropdown-item rounded-0 btnAction" id="reactivateuser" value="reactivateuser"><i class="pe-2 fa-solid fa-repeat text-success"></i> Reactivate user </button></li>';
            }

            $html .= '<li><button class="dropdown-item rounded-0 btnAction text-danger" id="deleteuser" value="deleteuser"><i class="pe-2 fa-solid fa-trash-can"></i> Delete </button></li>';
        } else {
            $html .= '<li><button class="dropdown-item rounded-0 btnAction" id="createuser" value="createuser"><i class="fa-solid fa-plus text-primary"></i> Create </button></li>';
        }

        $html .= '</ul></div></td></tr>';
    }

    return $html;
}

$toTable = ["SEARCH"];

// ✅ Safe input parsing
$usertype = isset($_POST['usertype']) ? $_POST['usertype'] : '';
$inputtype = isset($_POST['inputtype']) ? $_POST['inputtype'] : '';
$inputtext = isset($_POST['inputtext']) ? $_POST['inputtext'] : '';

$mapUser = ['student' => 2, 'instructor' => 1];
$usertype = $mapUser[$usertype] ?? 0;

// Default to empty mapping if type is unknown
$mapInfo = match ($usertype) {
    2 => ['lastname' => 'LAST_NAME', 'firstname' => 'FIRST_NAME'],
    1 => ['lastname' => 'LNAME', 'firstname' => 'FNAME'],
    default => []
};

$inputtype = $mapInfo[$inputtype] ?? '';
$inputtext = "%" . $inputtext . "%";

// ✅ Define your queries with required parameters list
$queries = [
    "STUDENT" => [
        "SEARCH" => [
            "query" => 
                "SELECT DISTINCT 
                    ENCRYPT_DATA(IFNULL(stud.`SchlStudSms_ID`, '')) `ID`,
                    stud.`SchlStud_NO` `NO`,
                    CONCAT(IFNULL(info.`SchlEnrollRegStudInfo_LAST_NAME`, ''), ', ', 
                    IFNULL(info.`SchlEnrollRegStudInfo_FIRST_NAME`, ''), ' ', 
                    IFNULL(info.`SchlEnrollRegStudInfo_MIDDLE_NAME`, '')) `NAME`,
                    IFNULL((SELECT us.`SysUser_ID` FROM `systemuser` us 
                        WHERE us.`SysUser_STATUS` = 1
                        AND us.`SysUserType_ID` = ?
                        AND us.`SchlUser_ID` = stud.`SchlStudSms_ID`), 0) `ACCOUNT`,
                    IFNULL((SELECT us.`SysUser_ISACTIVE` FROM `systemuser` us 
                        WHERE us.`SysUserType_ID` = ?
                        AND us.`SchlUser_ID` = stud.`SchlStudSms_ID`), 0) `SUSPENDED`,
                    IFNULL((SELECT us.`SysUser_ISACTIVE` FROM `systemuser` us 
                        WHERE us.`SysUserType_ID` = ?
                        AND us.`SchlUser_ID` = stud.`SchlStudSms_ID`), 0) `DELETED`

                    FROM `schoolenrollmentassessment` ass

                    LEFT JOIN `schoolstudent` stud
                    ON ass.`SchlStud_ID` = stud.`SchlStudSms_ID`
                    LEFT JOIN `schoolenrollmentregistrationstudentinformation` info
                    ON stud.`SchlEnrollRegColl_ID` = info.`SchlEnrollReg_ID`

                    WHERE ass.`SchlEnrollAss_STATUS` = 1
                    AND ass.`SchlEnrollWithdrawType_ID` = 0
                    AND stud.`SchlStud_ISACTIVE` =  1
                    AND stud.`SchlStud_STATUS` = 1
                    AND info.`SchlEnrollRegStudInfo_$inputtype` LIKE ?
                    
                    LIMIT 50;",
            "data_types" => "iiis",
            "param" => [$usertype, $usertype, $usertype, $inputtext],
            "required" => ['usertype', 'inputtype', 'inputtext']
        ]
    ],
    "INSTRUCTOR" => [
        "SEARCH" => [
            "query" => 
                "SELECT DISTINCT
                    ENCRYPT_DATA(IFNULL(emp.`SchlEmpSms_ID`, '')) `ID`,
                    emp.`SchlEmp_IDNO` `NO`,
                    CONCAT(IFNULL(emp.`SchlEmp_LNAME`,''), ', ',
                        IFNULL(emp.`SchlEmp_FNAME`,''), ' ',
                        IFNULL(emp.`SchlEmp_MNAME`,''))`NAME`,
                    IFNULL((SELECT us.`SysUser_ID` FROM `systemuser` us 
                        WHERE us.`SysUser_STATUS` = 1
                        AND us.`SysUserType_ID` = ?
                        AND us.`SchlUser_ID` = emp.`SchlEmpSms_ID`), 0) `ACCOUNT`,
                    IFNULL((SELECT us.`SysUser_ISACTIVE` FROM `systemuser` us 
                        WHERE us.`SysUserType_ID` = ?
                        AND us.`SchlUser_ID` = emp.`SchlEmpSms_ID`), 0) `SUSPENDED`,
                    IFNULL((SELECT us.`SysUser_ISACTIVE` FROM `systemuser` us 
                        WHERE us.`SysUserType_ID` = ?
                        AND us.`SchlUser_ID` = emp.`SchlEmpSms_ID`), 0) `DELETED`

                    FROM `schoolemployee` emp 

                    LEFT JOIN `systemuser` us
                    ON emp.`SchlEmpSms_ID` = us.`SchlUser_ID` AND us.`SysUserType_ID` = ?

                    WHERE emp.`SchlEmp_STATUS` = 1
                    AND emp.`SchlEmp_ISACTIVE` = 1
                    AND us.`SysUser_STATUS` = 1
                    AND emp.`SchlEmp_$inputtype` LIKE ?

                    ORDER BY `NAME` ASC
                    LIMIT 50;",
            "data_types" => "iiiis",
            "param" => [$usertype,$usertype,$usertype,$usertype, $inputtext],
            "required" => ['usertype', 'inputtext']
        ]
    ]


];



// ✅ Get and sanitize the type
$usertype1 = isset($_POST['usertype']) ? strtoupper(trim($_POST['usertype'])) : '';
$type = isset($_POST['type']) ? strtoupper(trim($_POST['type'])) : '';
$queries = $queries[$usertype1];

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
    if (in_array($type, $toTable)) {
        echo buildTableBody($result);
    } else {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        echo json_encode($data);
    }

    // Close
    $stmt->close();
} catch (Throwable $e) {
    // Don't echo raw error to the browser
    // You can log it instead: error_log($e->getMessage());
    echo 'An error occurred. Please try again.';
}
