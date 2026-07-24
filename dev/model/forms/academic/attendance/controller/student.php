<?php
session_start();
require('../config/conn-config.php');

$type = $_POST['type'];
$fetch = [];

if($type == "FETCH_ATTENDANCE_LOGS"){

    $USERID = $_SESSION['STUDENT']['ID'];
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

if ($type === 'GET_SUBJECT_LIST') {

    $USERID = $_SESSION['STUDENT']['ID'];
    $LVLID  = $_SESSION['STUDENT']['LVLID'];
    $YRID   = $_SESSION['STUDENT']['YRID'];
    $PRDID  = $_SESSION['STUDENT']['PRDID'];

    // $USERID = 957;
    // $LVLID  = 2;
    // $YRID   = 19;
    // $PRDID  = 6;

    $qry_get_subj_list = "SELECT `schl_enr_as`.`SchlAcadSubj_ID` AS `schl_acad_subj_id`
                          FROM `schoolstudent` `schl_stud`
                          LEFT JOIN `schoolenrollmentregistration` `schl_enr_reg`
                            ON `schl_stud`.`SchlEnrollRegColl_ID` = `schl_enr_reg`.`SchlEnrollRegSms_ID`
                          LEFT JOIN `schoolenrollmentregistrationstudentinformation` `schl_enr_reg_stud_info`
                            ON `schl_enr_reg`.`SchlEnrollRegSms_ID` = `schl_enr_reg_stud_info`.`SchlEnrollReg_ID`
                          LEFT JOIN `schoolenrollmentassessment` `schl_enr_as`
                            ON `schl_enr_reg`.`SchlStud_ID` = `schl_enr_as`.`SchlStud_ID`
                          WHERE `schl_stud`.`SchlStudSms_ID` = ?
                            AND `schl_enr_as`.`SchlAcadLvl_ID` = ?
                            AND `schl_enr_as`.`SchlAcadYr_ID` = ?
                            AND `schl_enr_as`.`SchlAcadPrd_ID` = ?
                            AND `schl_enr_reg`.`SchlAcadLvl_ID` = ?";

    $stmt = $dbConn->prepare($qry_get_subj_list);
    $stmt->bind_param("iiiii", $USERID, $LVLID, $YRID, $PRDID, $LVLID);
    $stmt->execute();
    $subj_rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $raw_ids = array_column($subj_rows, 'schl_acad_subj_id');
    $subj_ids = [];
    foreach ($raw_ids as $id) {
        foreach (explode(',', $id) as $single_id) {
            $subj_ids[] = (int) trim($single_id);
        }
    }
    $subj_ids = array_unique($subj_ids);

    if (empty($subj_ids)) {
        $fetch = [];
    } else {

    $placeholders = implode(',', array_fill(0, count($subj_ids), '?'));
    $types = str_repeat('i', count($subj_ids));

    $qry = "SELECT `schl_enr_subj_off`.`SchlEnrollSubjOffSms_ID` AS `subj_id`,
                `schl_acad_subj`.`SchlAcadSubj_CODE` AS `subj_code`,
                `schl_acad_subj`.`SchlAcadSubj_desc` AS `subj_desc`
            FROM schoolenrollmentsubjectoffered schl_enr_subj_off
            LEFT JOIN schoolacademicsubject schl_acad_subj
                ON schl_enr_subj_off.SchlAcadSubj_ID = schl_acad_subj.SchlAcadSubjSms_ID
            LEFT JOIN schooltadi studrec
                ON schl_enr_subj_off.SchlEnrollSubjOffSms_ID = studrec.schlenrollsubjoff_id
                AND DATE(studrec.schltadi_date) = CURDATE()
            WHERE
                schl_enr_subj_off.SchlEnrollSubjOffSms_ID IN ($placeholders)
            GROUP BY schl_enr_subj_off.SchlEnrollSubjOffSms_ID";

    $stmt = $dbConn->prepare($qry);
    $stmt->bind_param($types, ...$subj_ids);
    $stmt->execute();
    $fetch = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    }
}
echo json_encode($fetch);
?>