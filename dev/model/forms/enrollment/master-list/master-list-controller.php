<?php 
    /**
     * Builds HTML <option> tags for a <select> element using the same $data format as the table.
     * Uses the first column as value and second column as label.
     *
     * @param array $data The query result (array of associative arrays)
     * @return string The generated <option> tags
     */
    function buildDropdown(array $data, $type): string {
        $with_all = ['COURSE', 'YEARLEVEL', 'SECTION'];

        if (empty($data)) {
            return '<option value="">NONE</option>';
        }
        // Get first two column names
        $columns = array_keys($data[0]);
        $valueKey = $columns[0];
        $labelKey = $columns[1];

        $html = '';

        if(in_array($type, $with_all)){
            $values = array_map(fn($row) => htmlspecialchars($row[$valueKey]), $data);
            
            // Join them into a comma-separated string
            $ids = implode(',', $values);
            
            $html .= "<option value='$ids'>ALL</option>";
        }

        foreach ($data as $row) {
            $value = htmlspecialchars($row[$valueKey]);
            $label = htmlspecialchars($row[$labelKey]);
            $html .= "<option value='$value'>$label</option>";
        }

        return $html;
    }

    /**
     * Displays student records from a mysqli query result or inserts a default record if none exist.
     *
     * @param mysqli      $conn   The active database connection.
     * @param mysqli_result $result The result set from a SELECT query.
     * @return void
     */
    function buildTable(array $data): array {
        $html = '';

        if (empty($data)) {
            $html .= "<tr><td colspan='5' class='text-center text-danger'>No matching record found</td></tr>";
        }
        
        $counter = 1;
        foreach ($data as $row) {
            $html .= "<tr>
                        <td>" . $counter . "</td>
                        <td style='width: 15%'>" . $row['NUMBER'] . "</td>
                        <td>" . str_replace('Ñ', 'ñ', ucwords(strtolower($row['LAST_NAME']))) . "</td>
                        <td>" . str_replace('Ñ', 'ñ', ucwords(strtolower($row['FIRST_NAME']))) . "</td>
                        <td>" . str_replace('Ñ', 'ñ', ucwords(strtolower($row['MIDDLE_NAME']))) . "</td>
                        <td>" . ucwords(strtolower($row['GENDER'])) . "</td>
                        <td>" . ucwords(strtolower($row['YEAR_LEVEL'])) . "</td>
                        <td>" . $row['SECTION'] . "</td>
                        <td>" . ucwords(strtolower($row['ENROLLMENT_STATUS'])) . "</td>
                        <!-- <td><a class='link-opacity-100-hover' href='#'>See Info</a></td> -->
                    </tr>";

            $counter += 1;
        }
        
        return [
            'TABLE_COUNT' => count($data),
            'TABLE_CONTENT' => $html
        ];
    }

    // Build placeholders and types for array params
    function placeholders($array, $type = 'i') {
        return [
            implode(',', array_fill(0, count($array), '?')), // "?, ?, ?"
            str_repeat($type, count($array))                 // "iii"
        ];
    }

    // ✅ Secure cookie flags (must be set before session_start)
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.cookie_samesite', 'Strict');

    // PHP error handling
    // ini_set('display_errors', 0);
    // ini_set('log_errors', 1);

    session_start();
    require_once '../../../configuration/connection-config.php';

    // Check if session exists and validate user role
    if (!isset($_SESSION['FULL_NAME'])) {
        http_response_code(403);
        exit;
    }

    $id          = (int) ($_SESSION['EMPLOYEE']['ID'] ?? 0);
    $deptid      = $_SESSION['EMPLOYEE']['DEPID'] ?? '';
    $levelid     = isset($_POST['levelid']) ? $_POST['levelid'] : '';
    $yearid      = isset($_POST['yearid']) ? $_POST['yearid'] : '';
    $periodid    = isset($_POST['periodid']) ? $_POST['periodid'] : '';
    $yearlevelid = isset($_POST['yearlevelid']) ? explode(',', $_POST['yearlevelid']) : [];
    $courseid    = isset($_POST['courseid'])  ? explode(',', $_POST['courseid']) : [];
    $sectionid   = isset($_POST['sectionid'])  ? explode(',', $_POST['sectionid']) : [];

    $inputtype = isset($_POST['inputtype'])  ? strtoupper(str_replace(' ', '_', $_POST['inputtype']))  : '';
    $inputtext = isset($_POST['inputtext'])  ? $_POST['inputtext'].'%'  : '';

    [$ph_course, $types_course] = placeholders($courseid, 's');   
    [$ph_yearlevel, $types_yearlevel] = placeholders($yearlevelid, 's');   
    [$ph_section, $types_section] = placeholders($sectionid, 's');   

    // ✅ Define your queries with required parameters list
    $queries = [
        "LEVEL" => [
            "query" => "SELECT DISTINCT info.`SchlAcadLvlSms_ID` `ID`,
                        info.`SchlAcadLvl_NAME` `DESC`

                        FROM `schoolenrollmentassessment` ass 

                        LEFT JOIN `schoolacademiccourses` crse
                        ON ass.`SchlAcadCrse_ID` = crse.`SchlAcadCrseSms_ID`
                        LEFT JOIN `schoolacademiclevel` info
                        ON ass.`SchlAcadLvl_ID` = info.`SchlAcadLvlSms_ID`

                        WHERE ass.`SchlEnrollAss_STATUS` = 1
                        AND ass.`SchlEnrollWithdrawType_ID` = 0
                        AND crse.`SchlAcadCrses_STATUS` = 1
                        AND crse.`SchlAcadCrses_ISACTIVE` = 1
                        AND info.`SchlAcadLvl_STATUS` = 1
                        AND info.`SchlAcadLvl_ISACTIVE` = 1
                        AND IF(? = 19, TRUE, crse.`SchlDept_ID` = ?)

                        ORDER BY `DESC` DESC;",
            "data_types" => "ii",
            "param" => [$deptid, $deptid],
            "required" => ['deptid', 'deptid']
        ],
        "YEAR" => [
            "query" => "SELECT DISTINCT info.`SchlAcadYrSms_ID` `ID`,
                        info.`SchlAcadYr_NAME` `DESC`

                        FROM `schoolenrollmentassessment` ass 

                        LEFT JOIN `schoolacademiccourses` crse
                        ON ass.`SchlAcadCrse_ID` = crse.`SchlAcadCrseSms_ID`
                        LEFT JOIN `schoolacademicyear` info
                        ON ass.`SchlAcadYr_ID` = info.`SchlAcadYrSms_ID`

                        WHERE ass.`SchlEnrollAss_STATUS` = 1
                        AND ass.`SchlEnrollWithdrawType_ID` = 0
                        AND crse.`SchlAcadCrses_STATUS` = 1
                        AND crse.`SchlAcadCrses_ISACTIVE` = 1
                        AND info.`SchlAcadYr_STATUS` = 1
                        AND info.`SchlAcadYr_ISACTIVE` = 1
                        AND IF(? = 19, TRUE, crse.`SchlDept_ID` = ?)
                        AND ass.`SchlAcadLvl_ID` = ?

                        ORDER BY `DESC` DESC;",
            "data_types" => "iii",
            "param" => [$deptid, $deptid, $levelid],
            "required" => ['deptid', 'levelid']
        ],
        "PERIOD" => [
            "query" => "SELECT DISTINCT info.`SchlAcadPrdSms_ID` `ID`,
                        info.`SchlAcadPrd_NAME` `DESC`

                        FROM `schoolenrollmentassessment` ass 

                        LEFT JOIN `schoolacademiccourses` crse
                        ON ass.`SchlAcadCrse_ID` = crse.`SchlAcadCrseSms_ID`
                        LEFT JOIN `schoolacademicperiod` info
                        ON ass.`SchlAcadPrd_ID` = info.`SchlAcadPrdSms_ID`

                        WHERE ass.`SchlEnrollAss_STATUS` = 1
                        AND ass.`SchlEnrollWithdrawType_ID` = 0
                        AND crse.`SchlAcadCrses_STATUS` = 1
                        AND crse.`SchlAcadCrses_ISACTIVE` = 1
                        AND info.`SchlAcadPrd_STATUS` = 1
                        AND info.`SchlAcadPrd_ISACTIVE` = 1
                        AND IF(? = 19, TRUE, crse.`SchlDept_ID` = ?)
                        AND ass.`SchlAcadLvl_ID` = ?
                        AND ass.`SchlAcadYr_ID` = ?

                        ORDER BY `DESC` DESC;",
            "data_types" => "iiii",
            "param" => [$deptid, $deptid, $levelid, $yearid],
            "required" => ['deptid', 'levelid', 'yearid']
        ],
        "COURSE" => [
            "query" => "SELECT DISTINCT info.`SchlAcadCrseSms_ID` `ID`,
                        info.`SchlAcadCrses_NAME` `DESC`

                        FROM `schoolenrollmentassessment` ass 

                        LEFT JOIN `schoolacademiccourses` crse
                        ON ass.`SchlAcadCrse_ID` = crse.`SchlAcadCrseSms_ID`
                        LEFT JOIN `schoolacademiccourses` info
                        ON ass.`SchlAcadCrse_ID` = info.`SchlAcadCrseSms_ID`

                        WHERE ass.`SchlEnrollAss_STATUS` = 1
                        AND ass.`SchlEnrollWithdrawType_ID` = 0
                        AND crse.`SchlAcadCrses_STATUS` = 1
                        AND crse.`SchlAcadCrses_ISACTIVE` = 1
                        AND info.`SchlAcadCrses_STATUS` = 1
                        AND info.`SchlAcadCrses_ISACTIVE` = 1
                        AND IF(? = 19, TRUE, crse.`SchlDept_ID` = ?)
                        AND ass.`SchlAcadLvl_ID` = ?
                        AND ass.`SchlAcadYr_ID` = ?
                        AND ass.`SchlAcadPrd_ID` = ?

                        ORDER BY `DESC` ASC;",
            "data_types" => "iiiii",
            "param" => [$deptid, $deptid, $levelid, $yearid, $periodid],
            "required" => ['deptid', 'levelid', 'yearid', 'periodid']
        ],
        "YEARLEVEL" => [
            "query" => "SELECT DISTINCT info.`SchlAcadYrLvlSms_ID` `ID`,
                        info.`SchlAcadYrLvl_NAME` `DESC`

                        FROM `schoolenrollmentassessment` ass 

                        LEFT JOIN `schoolacademiccourses` crse
                        ON ass.`SchlAcadCrse_ID` = crse.`SchlAcadCrseSms_ID`
                        LEFT JOIN `schoolacademicyearlevel` info
                        ON ass.`SchlAcadYrLvl_ID` = info.`SchlAcadYrLvlSms_ID`

                        WHERE ass.`SchlEnrollAss_STATUS` = 1
                        AND ass.`SchlEnrollWithdrawType_ID` = 0
                        AND crse.`SchlAcadCrses_STATUS` = 1
                        AND crse.`SchlAcadCrses_ISACTIVE` = 1
                        AND info.`SchlAcadYrLvl_STATUS` = 1
                        AND info.`SchlAcadYrLvl_ISACTIVE` = 1
                        AND IF(? = 19, TRUE, crse.`SchlDept_ID` = ?)
                        AND ass.`SchlAcadLvl_ID` = ?
                        AND ass.`SchlAcadYr_ID` = ?
                        AND ass.`SchlAcadPrd_ID` = ?
                        AND ass.`SchlAcadCrse_ID` IN ($ph_course)

                        ORDER BY `DESC` ASC;",
            "data_types" => "iisss" . ($types_course ?? ''),
            "param" => [$deptid, $deptid, $levelid, $yearid, $periodid, ...$courseid],
            "required" => ['deptid', 'levelid', 'yearid', 'periodid', 'courseid']
        ],
        "SECTION" => [
            "query" => "SELECT DISTINCT info.`SchlAcadSecSms_ID` `ID`,
                        info.`SchlAcadSec_NAME` `DESC`

                        FROM `schoolenrollmentassessment` ass 

                        LEFT JOIN `schoolacademiccourses` crse
                        ON ass.`SchlAcadCrse_ID` = crse.`SchlAcadCrseSms_ID`
                        LEFT JOIN `schoolacademicsection` info
                        ON ass.`SchlAcadSec_ID` = info.`SchlAcadSecSms_ID`

                        WHERE ass.`SchlEnrollAss_STATUS` = 1
                        AND ass.`SchlEnrollWithdrawType_ID` = 0
                        AND crse.`SchlAcadCrses_STATUS` = 1
                        AND crse.`SchlAcadCrses_ISACTIVE` = 1
                        AND info.`SchlAcadSec_STATUS` = 1
                        AND info.`SchlAcadSec_ISACTIVE` = 1
                        AND IF(? = 19, TRUE, crse.`SchlDept_ID` = ?)
                        AND ass.`SchlAcadLvl_ID` = ?
                        AND ass.`SchlAcadYr_ID` = ?
                        AND ass.`SchlAcadPrd_ID` = ?
                        AND ass.`SchlAcadCrse_ID` IN ($ph_course)
                        AND ass.`SchlAcadYrLvl_ID` IN ($ph_yearlevel)

                        ORDER BY `DESC` ASC;",
            "data_types" => "iisss" . ($types_course ?? '') . ($types_yearlevel ?? ''),
            "param" => [$deptid, $deptid, $levelid, $yearid, $periodid, ...$courseid, ...$yearlevelid],
            "required" => ['deptid', 'levelid', 'yearid', 'periodid', 'courseid', 'yearlevelid']
        ],
        "DISPLAY" => [
            "query" => "SELECT DISTINCT stud.`SchlStud_IDNO` `NUMBER`,
                        info.`SchlEnrollRegStudInfo_LAST_NAME` `LAST_NAME`,
                        info.`SchlEnrollRegStudInfo_FIRST_NAME` `FIRST_NAME`,
                        info.`SchlEnrollRegStudInfo_MIDDLE_NAME` `MIDDLE_NAME`,
                        info.`SchlEnrollRegStudInfo_GENDER` `GENDER`,
                        yrlvl.`SchlAcadYrLvl_NAME` `YEAR_LEVEL`, 
                        sec.`SchlAcadSec_CODE` `SECTION`, 
                        (SELECT IF(inv.`SchlEnrollInvSms_ID`, 'ENROLLED', 'NOT ENROLLED')

                        FROM `schoolenrollmentinvoice` inv

                        WHERE inv.`SchlEnrollInv_STATUS` = 1
                        AND inv.`SchlEnrollInv_ISACTIVE` = 1
                        AND inv.`SchlEnrollInv_ISCANCEL` = 0
                        AND inv.`SchlEnrollPayType_ID` > 0
                        AND inv.`SchlAcadLvl_ID` = ?
                        AND inv.`SchlAcadYr_ID` = ?
                        AND inv.`SchlAcadPrd_ID` = ?
                        AND inv.`SchlEnrollAss_ID` = ass.`SchlEnrollAssSms_ID`
                        AND inv.`SchlStud_ID` = stud.`SchlStudSms_ID`

                        ORDER BY inv.`SchlEnrollInvSms_ID` ASC
                        LIMIT 1) `ENROLLMENT_STATUS`

                        FROM `schoolenrollmentassessment` ass

                        LEFT JOIN `schoolstudent` stud
                        ON stud.`SchlStudSms_ID` = ass.`SchlStud_ID`
                        LEFT JOIN `schoolenrollmentregistrationstudentinformation` info
                        ON stud.`SchlEnrollRegColl_ID` = info.`SchlEnrollReg_ID`
                        LEFT JOIN `schoolacademicyearlevel` yrlvl
                        ON ass.`SchlAcadYrLvl_ID` = yrlvl.`SchlAcadYrLvlSms_ID`
                        LEFT JOIN `schoolacademicsection` sec
                        ON ass.`SchlAcadSec_ID` = sec.`SchlAcadSecSms_ID`

                        WHERE ass.`SchlEnrollWithdrawType_ID` = 0
                        AND ass.`SchlEnrollAss_STATUS` = 1
                        AND stud.`SchlStud_STATUS` = 1
                        AND stud.`SchlStud_ISACTIVE` = 1
                        AND ass.`SchlAcadLvl_ID` = ?
                        AND ass.`SchlAcadYr_ID` = ?
                        AND ass.`SchlAcadPrd_ID` = ?
                        AND ass.`SchlAcadCrse_ID` IN ($ph_course)
                        AND ass.`SchlAcadYrLvl_ID` IN ($ph_yearlevel)
                        AND ass.`SchlAcadSec_ID` IN ($ph_section)
                        AND info.`SchlEnrollRegStudInfo_$inputtype` LIKE ?

                        ORDER BY `SECTION`, `LAST_NAME` ASC;",
            "data_types" => "iiiiii" . ($types_course ?? '') . ($types_yearlevel ?? '') . ($types_section ?? '') . 's',
            "param" => [$levelid, $yearid, $periodid, $levelid, $yearid, $periodid, ...$courseid, ...$yearlevelid,  ...$sectionid, $inputtext],
            "required" => ['levelid', 'yearid', 'periodid', 'courseid', 'yearlevelid',   'sectionid', 'inputtext']
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
            echo json_encode(buildTable($data));
        }

        if($type !== 'DISPLAY'){
            echo buildDropdown($data, $type);
        }
    } catch (Throwable $e) {
        // Don't echo raw error to the browser
        // You can log it instead: error_log($e->getMessage());
        echo $e->getMessage();
        echo 'An error occurred. Please try again.';
    }

?>