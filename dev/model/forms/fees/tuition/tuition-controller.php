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
if(!isset($_SESSION['STUDENT']['ID'])){
    die("Unauthorized access!");
}

require_once 'tuition-functions.php';

// ✅ Safe input parsing
$id = $_SESSION['STUDENT']['ID'];
$levelid = isset($_POST['levelid']) ? $_POST['levelid'] : 0;
$yearid = isset($_POST['yearid']) ? $_POST['yearid'] : 0;
$periodid = isset($_POST['periodid']) ? $_POST['periodid'] : 0;
$courseid = isset($_POST['courseid']) ? $_POST['courseid'] : 0;

// ✅ Define your queries with required parameters list
$queries = [
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
    "DISPLAY" => [
        "query" => "CALL `spDisplayStudentGrades`(?,
                        `DECRYPT_DATA`(?),
                        `DECRYPT_DATA`(?),
                        `DECRYPT_DATA`(?),
                        `DECRYPT_DATA`(?),
                        0,0);",
        "data_types" => "issss",
        "param" => [$id, $levelid, $yearid, $periodid, $courseid],
        "required" => ['id', 'levelid', 'yearid', 'periodid', 'courseid']
    ]
];

// ✅ Get and sanitize the type
$type = isset($_POST['type']) ? strtoupper(trim($_POST['type'])) : '';

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
        if ($type === 'DISPLAY') {
            echo json_encode($data);
        }

        if ($type !== 'DISPLAY') {
            populateDropdown($data);
        }
    } catch (Throwable $e) {
        // Don't echo raw error to the browser
        // You can log it instead: error_log($e->getMessage());
        exit('An error occurred. Please try again.');
    }
    // die('Error: Invalid parameter.');
}

if($type === 'TUITION FEE'){
    $queries = [
        "payment scheme" => [
            "query" => "SELECT ass.`SchlEnrollPaySchm_ID` `PLAN_ID`,
                            plan.`SchlEnrollPaySchm_NAME` `PLAN_NAME`,
                            plan.`SchlEnrollPaySchm_DISCOUNT` `DISCOUNT`,
                            plan.`SchlEnrollPaySchm_DOWNPAY` `DOWN_PAYMENT`,
			                plan.`SchlEnrollPaySchm_PLAN_DET` `PLAN_DETAIL`,
                            plan_det.`SchlEnrollPaySchmDetSms_ID` `SCHEME_ID`,
                            plan_det.`SchlEnrollPaySchmDet_NAME` `SCHEME_NAME`,
                            plan_det.`SchlEnrollPaySchmDet_AMT` `SCHEME_AMNT`,
                            plan_det.`SchlEnrollPaySchmDet_ISTUITION_FEE` `IS_TUITION_FEE`

                        FROM `schoolenrollmentassessment` ass 

                        LEFT JOIN `schoolenrollmentadmission` adm
                        ON ass.`SchlEnrollAdm_ID` = adm.`SchlEnrollAdmSms_ID`
                        LEFT JOIN `schoolenrollmentpaymentscheme` plan 
                        ON ass.`SchlEnrollPaySchm_ID` = plan.`SchlEnrollPaySchmSms_ID`
                        LEFT JOIN `schoolenrollmentpaymentschemedetail` plan_det 
                        ON plan.`SchlEnrollPaySchmSms_ID` = plan_det.`SchlEnrollPayPlanDet_ID`
                        AND adm.`SchlEnrollCat_ID` = plan_det.`SchlEnrollCat_ID`
                        AND adm.`SchlEnrollAdmColl_IS_TRANSFEREE` = plan_det.`SchlEnrollPaySchmDet_IS_TRANSFEREE`

                        WHERE ass.`SchlEnrollAss_STATUS` = 1
                        AND ass.`SchlEnrollWithdrawType_ID` = 0
                        AND plan.`SchlEnrollPaySchm_STATUS` = 1
                        AND plan.`SchlEnrollPaySchm_ISACTIVE` = 1
                        AND plan_det.`SchlEnrollPaySchmDet_STATUS` = 1
                        AND plan_det.`SchlEnrollPaySchmDet_ISACTIVE` = 1
                        AND ass.`SchlStud_ID` = ?
                        AND ass.`SchlAcadLvl_ID` = ?
                        AND ass.`SchlAcadYr_ID` = ?
                        AND ass.`SchlAcadPrd_ID` = ?
                        AND ass.`SchlAcadCrse_ID` = ?",
            "data_types" => "iiiii",
            "param" => [$id, $levelid, $yearid, $periodid, $courseid],
            "required" => ['id', 'levelid', 'yearid', 'periodid', 'courseid']
        ],
        "subject offered" => [
            "query" => "SELECT IFNULL(off.`SchlEnrollSubjOffSms_ID`, 0) `ID`,
                            IFNULL(off.`SchlEnrollSubjOff_UNIT`, 0) `TOTAL_UNIT`,

                            IFNULL(off.`SchlEnrollSubjOff_LEC`, 0) `LEC_UNIT`,
                            IFNULL(off.`SchlEnrollSubjOff_LEC_FEE`, 0) `LEC_FEE`,
                            IFNULL(off.`SchlEnrollSubjOff_LEC_INCLDUNITFORCOMPUT`, 0) `LEC_INCLUDE`,
                            IFNULL(off.`SchlEnrollSubjOff_LEC_ISVIEW`, 0) `LEC_VIEW`,
                            IFNULL(off.`SchlEnrollSubjOff_LEC_SUB_FEE`, 0) `LEC_SUB_FEE`,
                            IFNULL(off.`SchlEnrollSubjOff_LEC_USE_ALIAS`, 0) `LEC_USE_ALIAS`,
                            IFNULL(off.`SchlEnrollSubjOff_LEC_ALIAS_NAME`, 0) `LEC_ALIAS`,

                            IFNULL(off.`SchlEnrollSubjOff_LAB`, 0) `LAB_UNIT`,
                            IFNULL(off.`SchlEnrollSubjOff_LAB_FEE`, 0) `LAB_FEE`,
                            IFNULL(off.`SchlEnrollSubjOff_LAB_INCLDUNITFORCOMPUT`, 0) `LAB_INCLUDE`,
                            IFNULL(off.`SchlEnrollSubjOff_LAB_ISVIEW`, 0) `LAB_VIEW`,
                            IFNULL(off.`SchlEnrollSubjOff_LAB_SUB_FEE`, 0) `LAB_SUB_FEE`,
                            IFNULL(off.`SchlEnrollSubjOff_LAB_USE_ALIAS`, 0) `LAB_USE_ALIAS`,
                            IFNULL(off.`SchlEnrollSubjOff_LAB_ALIAS_NAME`, 0) `LAB_ALIAS`,

                            IFNULL(off.`SchlEnrollSubjOff_SL`, 0) `SL_UNIT`,
                            IFNULL(off.`SchlEnrollSubjOff_SL_FEE`, 0) `SL_FEE`,
                            IFNULL(off.`SchlEnrollSubjOff_SL_INCLDUNITFORCOMPUT`, 0) `SL_INCLUDE`,
                            IFNULL(off.`SchlEnrollSubjOff_SL_ISVIEW`, 0) `SL_VIEW`,
                            IFNULL(off.`SchlEnrollSubjOff_SL_SUB_FEE`, 0) `SL_SUB_FEE`,
                            IFNULL(off.`SchlEnrollSubjOff_SL_USE_ALIAS`, 0) `SL_USE_ALIAS`,
                            IFNULL(off.`SchlEnrollSubjOff_SL_ALIAS_NAME`, 0) `SL_ALIAS`,

                            IFNULL(off.`SchlEnrollSubjOff_C`, 0) `C_UNIT`,
                            IFNULL(off.`SchlEnrollSubjOff_C_FEE`, 0) `C_FEE`,
                            IFNULL(off.`SchlEnrollSubjOff_C_INCLDUNITFORCOMPUT`, 0) `C_INCLUDE`,
                            IFNULL(off.`SchlEnrollSubjOff_C_ISVIEW`, 0) `C_VIEW`,
                            IFNULL(off.`SchlEnrollSubjOff_C_SUB_FEE`, 0) `C_SUB_FEE`,
                            IFNULL(off.`SchlEnrollSubjOff_C_USE_ALIAS`, 0) `C_USE_ALIAS`,
                            IFNULL(off.`SchlEnrollSubjOff_C_ALIAS_NAME`, 0) `C_ALIAS`,

                            IFNULL(off.`SchlEnrollSubjOff_RLE`, 0) `RLE_UNIT`,
                            IFNULL(off.`SchlEnrollSubjOff_RLE_FEE`, 0) `RLE_FEE`,
                            IFNULL(off.`SchlEnrollSubjOff_RLE_INCLDUNITFORCOMPUT`, 0) `RLE_INCLUDE`,
                            IFNULL(off.`SchlEnrollSubjOff_RLE_ISVIEW`, 0) `RLE_VIEW`,
                            IFNULL(off.`SchlEnrollSubjOff_RLE_SUB_FEE`, 0) `RLE_SUB_FEE`,
                            IFNULL(off.`SchlEnrollSubjOff_RLE_USE_ALIAS`, 0) `RLE_USE_ALIAS`,
                            IFNULL(off.`SchlEnrollSubjOff_RLE_ALIAS_NAME`, 0) `RLE_ALIAS`,

                            IFNULL(off.`SchlEnrollSubjOff_AFF`, 0) `AFF_UNIT`,
                            IFNULL(off.`SchlEnrollSubjOff_AFF_FEE`, 0) `AFF_FEE`,
                            IFNULL(off.`SchlEnrollSubjOff_AFF_INCLDUNITFORCOMPUT`, 0) `AFF_INCLUDE`,
                            IFNULL(off.`SchlEnrollSubjOff_AFF_ISVIEW`, 0) `AFF_VIEW`,
                            IFNULL(off.`SchlEnrollSubjOff_AFF_SUB_FEE`, 0) `AFF_SUB_FEE`,
                            IFNULL(off.`SchlEnrollSubjOff_AFF_USE_ALIAS`, 0) `AFF_USE_ALIAS`,
                            IFNULL(off.`SchlEnrollSubjOff_AFF_ALIAS_NAME`, 0) `AFF_ALIAS`,
                            
                            IFNULL(off.`SchlEnrollSubjOff_OTHER`, 0) `OTHER_UNIT`,
                            IFNULL(off.`SchlEnrollSubjOff_OTHER_FEE`, 0) `OTHER_FEE`,
                            IFNULL(off.`SchlEnrollSubjOff_OTHER_INCLDUNITFORCOMPUT`, 0) `OTHER_INCLUDE`,
                            IFNULL(off.`SchlEnrollSubjOff_OTHER_ISVIEW`, 0) `OTHER_VIEW`,
                            IFNULL(off.`SchlEnrollSubjOff_OTHER_SUB_FEE`, 0) `OTHER_SUB_FEE`,
                            IFNULL(off.`SchlEnrollSubjOff_OTHER_USE_ALIAS`, 0) `OTHER_USE_ALIAS`,
                            IFNULL(off.`SchlEnrollSubjOff_OTHER_ALIAS_NAME`, 0) `OTHER_ALIAS`,

                            IFNULL(off.`SchlEnrollSubjOff_ASTUTORIAL`, 0) `ASTUTORIAL`,
                            IFNULL(off.`SchlEnrollSubjOff_TTL_FEE_AMT`, 0) `TUTORIAL_AMOUNT`,

                            IFNULL(off.`SchlEnrollSubjOffAsTutorialApplytoAmount_LEC`, 0) `TUTORIAL_LEC_APPLY`,
                            IFNULL(off.`SchlEnrollSubjOffAsTutorialPercent_LEC`, 0) `TUTORIAL_LEC_PERCENT`,

                            IFNULL(off.`SchlEnrollSubjOffAsTutorialApplytoAmount_LAB`, 0) `TUTORIAL_LAB_APPLY`,
                            IFNULL(off.`SchlEnrollSubjOffAsTutorialPercent_LAB`, 0) `TUTORIAL_LAB_PERCENT`,

                            IFNULL(off.`SchlEnrollSubjOffAsTutorialApplytoAmount_SL`, 0) `TUTORIAL_SL_APPLY`,
                            IFNULL(off.`SchlEnrollSubjOffAsTutorialPercent_SL`, 0) `TUTORIAL_SL_PERCENT`,

                            IFNULL(off.`SchlEnrollSubjOffAsTutorialApplytoAmount_C`, 0) `TUTORIAL_C_APPLY`,
                            IFNULL(off.`SchlEnrollSubjOffAsTutorialPercent_C`, 0) `TUTORIAL_C_PERCENT`,

                            IFNULL(off.`SchlEnrollSubjOffAsTutorialApplytoAmount_RLE`, 0) `TUTORIAL_RLE_APPLY`,
                            IFNULL(off.`SchlEnrollSubjOffAsTutorialPercent_RLE`, 0) `TUTORIAL_RLE_PERCENT`,

                            IFNULL(off.`SchlEnrollSubjOffAsTutorialApplytoAmount_AFF`, 0) `TUTORIAL_AFF_APPLY`,
                            IFNULL(off.`SchlEnrollSubjOffAsTutorialPercent_AFF`, 0) `TUTORIAL_AFF_PERCENT`,

                            IFNULL(off.`SchlEnrollSubjOffAsTutorialApplytoAmount_OTHER`, 0) `TUTORIAL_OTHER_APPLY`,
                            IFNULL(off.`SchlEnrollSubjOffAsTutorialPercent_OTHER`, 0) `TUTORIAL_OTHER_PERCENT`,

                            IFNULL(off.`SchlEnrollSubjOff_USE_SUBJ_UNIT_AMT`, 0) `USE_SUBJ_UNIT_AMT`,
                            IFNULL(off.`SchlAcadSubjCatTyp_ID`, 0) `IS_NSTP`

                        FROM `schoolenrollmentassessment` ass 

                        LEFT JOIN `schoolenrollmentsubjectoffered` off
                        ON FIND_IN_SET(off.`SchlEnrollSubjOffSms_ID`, ass.`SchlAcadSubj_ID`)

                        WHERE ass.`SchlEnrollAss_STATUS` = 1
                        AND ass.`SchlEnrollWithdrawType_ID` = 0
                        AND off.`SchlEnrollSubjOff_STATUS` = 1
                        AND off.`SchlEnrollSubjOff_ISACTIVE` = 1
                        AND ass.`SchlStud_ID` = ?
                        AND ass.`SchlAcadLvl_ID` = ?
                        AND ass.`SchlAcadYr_ID` = ?
                        AND ass.`SchlAcadPrd_ID` = ?
                        AND ass.`SchlAcadCrse_ID` = ?",
            "data_types" => "iiiii",
            "param" => [$id, $levelid, $yearid, $periodid, $courseid],
            "required" => ['id', 'levelid', 'yearid', 'periodid', 'courseid']
        ],
        "additional" => [
            "query" => "SELECT IFNULL(addtnl.`SchlEnrollAddtnlFeeType_NAME`, '') `NAME`,
                            SUBSTRING_INDEX(SUBSTRING_INDEX(ass.`SchlEnrollAss_ADDTNLFEE`, '|:|', 2), '|:|', -1) `AMOUNT`


                        FROM `schoolenrollmentassessment` ass

                        LEFT JOIN `schoolenrollmentadditionalfeetype` addtnl
                        ON SUBSTRING_INDEX(ass.`SchlEnrollAss_ADDTNLFEE`, '|:|', 1) = addtnl.`SchlEnrollAddtnlFeeTypeSms_ID`

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
        "deduction" => [
            "query" => "SELECT IFNULL(deduct.`SchlEnrollDeducType_NAME`, '') `NAME`,
                            SUBSTRING_INDEX(SUBSTRING_INDEX(ass.`SchlEnrollAss_DEDUCTION`, '|:|', 2), '|:|', -1) `AMOUNT`


                        FROM `schoolenrollmentassessment` ass

                        LEFT JOIN `schoolenrollmentdeductiontype` deduct
                        ON SUBSTRING_INDEX(ass.`SchlEnrollAss_DEDUCTION`, '|:|', 1) = deduct.`SchlEnrollDeducTypeSms_ID`

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
        "transaction history" => [
            "query" => "SELECT inv.`SchlEnrollInv_DATE` `TRANSACTION_DATE`,
                            paytype.`SchlEnrollPayType_NAME` `PARTICULARS`,
                            paymode.`SchlEnrollPayMode_NAME` `PAYMENT_MODE`,
                            inv.`SchlEnrollInv_ORNO` `OR_NUMBER`,
                            inv.`SchlEnrollInv_AMT_TENDERED` `AMOUNT_TENDERED`

                        FROM `schoolenrollmentinvoice` inv 

                        LEFT JOIN `schoolenrollmentpaymentmode` paymode
                        ON inv.`SchlEnrollPayMode_ID` = paymode.`SchlEnrollPayModeSms_ID`
                        LEFT JOIN `schoolenrollmentpaymenttype` paytype
                        ON inv.`SchlEnrollPayType_ID` = paytype.`SchlEnrollPayTypeSms_ID`

                        WHERE inv.`SchlEnrollInv_ISACTIVE` = 1
                        AND inv.`SchlEnrollInv_STATUS` = 1
                        AND inv.`SchlEnrollInv_ISCANCEL` = 0
                        AND paymode.`SchlEnrollPayMode_STATUS` = 1
                        AND paymode.`SchlEnrollPayMode_ISACTIVE` = 1
                        AND paytype.`SchlEnrollPayType_STATUS` = 1
                        AND paytype.`SchlEnrollPayType_ISACTIVE` = 1
                        AND inv.`SchlStud_ID` = ?
                        AND inv.`SchlEnrollAss_ID` = (
                            SELECT ass.`SchlEnrollAssSms_ID`
                            FROM `schoolenrollmentassessment` ass 
                            WHERE ass.`SchlEnrollAss_STATUS` = 1
                            AND ass.`SchlEnrollWithdrawType_ID` = 0
                            AND ass.`SchlStud_ID` = ?
                            AND ass.`SchlAcadLvl_ID` = ?
                            AND ass.`SchlAcadYr_ID` = ?
                            AND ass.`SchlAcadPrd_ID` = ?
                            AND ass.`SchlAcadCrse_ID` = ?
                        )

                        ORDER BY `TRANSACTION_DATE` ASC;",
            "data_types" => "iiiiii",
            "param" => [$id, $id, $levelid, $yearid, $periodid, $courseid],
            "required" => ['id', 'id', 'levelid', 'yearid', 'periodid', 'courseid']
        ],
    ];

    $data = [];

    foreach($queries as $key => $queryConfig){
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
        
        // Execute
        $stmt->execute();

        // Get result (if SELECT)
        $result = $stmt->get_result();
        if ($result) {
            $data[$key] = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
        }

        // Close
        $stmt->close();
    }
    echo json_encode($data);

    // echo json_encode([
    //     array_to_table(college_tuition($data)), 
    //     to_transaction_table(college_transaction_history($data)),
    //     array_to_table(college_payment_plan($data, college_transaction_history($data)))
    // ]);
    
}

$dbConn->close();