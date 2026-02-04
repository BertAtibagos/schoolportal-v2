<?php

session_start();
include '../../configuration/connection-config.php';

if ( isset($_POST['gscale_name']) && isset($_POST['gscale_code']) && isset($_POST['details']) && isset($_POST['pass_score']) && 
    isset($_POST['levelid']) && isset($_POST['yearid']) && isset($_POST['periodid']) && isset($_POST['courseid']) && 
    isset($_POST['id']) && isset($_POST['comp_name']) && isset($_POST['comp_code']) && isset($_POST['comp_desc']) && isset($_POST['comp_percent']) && isset($_POST['sc'])) {
    $gscale_name =  $_POST['gscale_name'];
    $gscale_code =  $_POST['gscale_code'];
    $details = $_POST['details'];
    $pass_score = $_POST['pass_score'];
    $levelid = $_POST['levelid'];
    $periodid = $_POST['periodid'];
    $yearid = $_POST['yearid'];
    $courseid = $_POST['courseid'];
    $userid = isset($_SESSION['EMPLOYEE']['ID']) ? $_SESSION['EMPLOYEE']['ID'] : -1;
    $id = $_POST['id'];
    $deptid = $_POST['deptid'];
    $comp_name = $_POST['comp_name'];
    $comp_code = $_POST['comp_code'];
    $comp_desc = $_POST['comp_desc'];
    $comp_percent = $_POST['comp_percent'];
    $subcomp = $_POST['sc'];

    if ( $gscale_code !== '' && $gscale_name !== ''  && $pass_score !== '' && 
            $levelid !== '' && $yearid !== '' && $periodid !== '' && $courseid !== '' && $userid !== -1 && 
            $id !== '' && $comp_name !== '' && $comp_code !== ''  && $comp_percent !== '' && $subcomp !== '') {

        $sql = "INSERT INTO schoolacademicgradingscale (schlacadgradscale_code, 
                   schlacadgradscale_name, 
                  schlacadgradscale_desc, 
                  schlacadgradscale_datetime, 
                  schlacadgradscale_pass_score, 
                  schlacadgradscale_status, 
                  schlacadgradscale_isactive, 
                  schlacadgradscale_ispublish, 
                  schldept_id, 
                  schlacadyr_id, 
                  schlacadprd_id, 
                  schlacadlvl_id, 
                  schlacadcrse_id, 
                  schlacadcurr_id, 
                  schlacaduser_id)
                VALUES ('$gscale_code', 
                  '$gscale_name', 
                  '$details', 
                  NOW(), 
                  '$pass_score', 
                  1, 
                  1, 
                  1, 
                  '$deptid', 
                  '$yearid', 
                  '$periodid', 
                  '$levelid', 
                  '$courseid', 
                  9, 
                  '$userid'
                );";

        if (mysqli_query($dbConn, $sql)) {
            $last_id = mysqli_insert_id($dbConn);

            $array_sc = explode(':', $subcomp);
            for ($i = 0; $i < $id; $i++) {
                // echo '<script>alert("For Loop: '.$i.'")</script>';
                $comp_name = $_POST['comp_name'][$i];
                $comp_code = $_POST['comp_code'][$i];
                $comp_desc = $_POST['comp_desc'][$i];
                $comp_percent = $_POST['comp_percent'][$i];
                $rank = $i + 1;

                $sql1 = "INSERT INTO schoolacademicgradingscaledetail (SchlAcadGradScaleDet_CODE, 
                          SchlAcadGradScaleDet_NAME, 
                          SchlAcadGradScaleDet_DESC, 
                          SchlAcadGradScaleDet_RANKNO, 
                          SchlAcadGradScaleDet_PERCENTAGE, 
                          SchlAcadGradScaleDet_STATUS, 
                          SchlAcadGradScaleDet_ISACTIVE,
                          SchlAcadGradScaleDet_PARENT_ID,
                          SchlAcadGradScale_ID)
                          VALUES ( 
                          '$comp_code', 
                          '$comp_name', 
                          '$comp_desc', 
                          '$rank', 
                          '$comp_percent', 
                          1, 
                          1, 
                          0, 
                          '$last_id'
            );";

                if (mysqli_query($dbConn, $sql1)) {
                    $last_id1 = mysqli_insert_id($dbConn);
                    // $last_id2 = mysqli_insert_id($dbConn);

                    $array_sc1 = explode("|", $array_sc[$i]);

                    for ($j = 0; $j < count($array_sc1) - 1; $j++) {
                        $array_sc2 = explode(",", $array_sc1[$j]);

                        $subcomp_code  = $array_sc2[1];
                        $subcomp_name = $array_sc2[0];
                        $subcomp_desc = $array_sc2[2];
                        $subcomp_percent = $array_sc2[3];
                        $rank_sc = $j + 1;
                        // echo '<script>alert("'.$subcomp_code.'+'.$subcomp_name.'+'.$subcomp_desc.'+'.$subcomp_percent.'+")</script>';

                        if ($subcomp_name !== '' && $subcomp_code !== '' && $subcomp_percent !== '') {
                            $sql2 = "INSERT INTO schoolacademicgradingscaledetail ( 
                                SchlAcadGradScaleDet_NAME,
                                SchlAcadGradScaleDet_CODE,
                                SchlAcadGradScaleDet_DESC, 
                                SchlAcadGradScaleDet_RANKNO, 
                                SchlAcadGradScaleDet_PERCENTAGE, 
                                SchlAcadGradScaleDet_STATUS, 
                                SchlAcadGradScaleDet_ISACTIVE,
                                SchlAcadGradScaleDet_PARENT_ID,
                                SchlAcadGradScale_ID)
                                VALUES ( 
                                '$subcomp_name', 
                                '$subcomp_code', 
                                '$subcomp_desc', 
                                '$rank_sc', 
                                '$subcomp_percent', 
                                1, 
                                1, 
                                '$last_id1',
                                '$last_id');";

                            if (mysqli_query($dbConn, $sql2)) {
                                // echo '<script>alert("Grading Scale Created.")</script>';
                                // echo '<script>window.location.reload();</script>';

                            }
                        } else {
                            echo '<script>alert("There are BLANK Subcomponent INPUTS.")</script>';
                        }
                    }

                    // echo '<script>alert("Component Data Upload to DB Successful.")</script>';

                } else {
                    echo '<script>alert("Component #' . $i . ' Data Upload to DB Unsuccessful.")</script>';
                }
            }
            
            $status = isset($last_id) ? "SUCCESS" : "FAILED";
            // Do Logging here
            $syntax = json_encode([
                "status" => $status,
                "id" => $userid,
                "gscale_id" => $last_id
            ]);

            $module = 'CREATE GRADING SCALE';
            $operation = 'GRADING SCALE CREATION';
            
            $stmt = $dbConn->prepare("CALL `spInsertLogs`(?, ?, ?, ?, 'systemuser')");
            $stmt->bind_param("isss", $userid, $module, $operation, $syntax);
            $stmt->execute();
        } else {
            echo '<script>alert("Data Upload to DB Unsuccessful.")</script>';
        }
    } else {
        echo '<script>alert("There are BLANK INPUTS.")</script>';
    }
} else {
    echo '<script>alert("Grading Scale Create Failed. \nContact ICT DEPARTMENT.")</script>';
}

$dbConn->close();
// echo '<script>alert("Grading Scale Created.")</script>';
