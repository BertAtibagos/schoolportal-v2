<?php
session_start();
include '../../configuration/connection-config.php';

if( isset($_POST['subjid']) && isset($_POST['gscaleid'])){

    $gscaleid = $_POST['gscaleid'];
    $subjid = $_POST['subjid'];
    $userid = isset($_SESSION['EMPLOYEE']['ID']) ? $_SESSION['EMPLOYEE']['ID'] : -1;

    if( $subjid !== "" && $gscaleid !== ""){
        $status = "SUCCESS";

        for($i = 0; $i < count($subjid); $i++){
            $subjidtag = $subjid[$i];

            $sql2 = "DELETE FROM `schoolacademicgradingscalesubject` WHERE `SchlEnrollSubjOff_ID` = '$subjidtag' OR SchlAcadGradScale_ID = '$gscaleid';";
            
            if(!mysqli_query($dbConn, $sql2)){
                $status = "FAILED";
                echo '<script>alert("ERROR: Tagging Unsuccessful!")</script>';
                break;
            }
        }

        for($j = 0; $j<count($subjid); $j++){
            $subjidtag = $subjid[$j];

            $sql = "INSERT INTO schoolacademicgradingscalesubject (SchlAcadGradScaleSubj_STATUS, 
                            SchlAcadGradScaleSubj_ISACTIVE, 
                            SchlAcadGradScale_ID, 
                            SchlEnrollSubjOff_ID)
                    VALUES (1, 
                            1, 
                            '$gscaleid', 
                            '$subjidtag')";
            
            if(!mysqli_query($dbConn, $sql)){
                $status = "FAILED";
                echo '<script>alert("ERROR: Tagging Unsuccessful!")</script>';
                break;
            }
        }

        // Do Logging here
        $syntax = json_encode([
            "status" => $status,
            "id" => $userid,
            "gscale_id" => $gscaleid, 
            "subj_id" => implode(',', $subjid)
        ]);

        $module = 'ASSIGN GRADING SCALE';
        $operation = 'ASSIGNING GRADING SCALE TO COURSES';
        
        $stmt = $dbConn->prepare("CALL `spInsertLogs`(?, ?, ?, ?, 'systemuser')");
        $stmt->bind_param("isss", $userid, $module, $operation, $syntax);
        $stmt->execute();
        
    } else {
        echo '<script>alert("ERROR: Blank input detected.")</script>';
    }
    
} else {
    echo '<script>alert("Variables are not set.")</script>';
}

$dbConn->close();
?>