<?php
session_start();
include '../../configuration/connection-config.php';

if(isset($_POST['gscale_name'])
&& isset($_POST['gscale_id'])
&& isset($_POST['gscale_code'])
&& isset($_POST['gscale_desc'])
&& isset($_POST['pass_score'])
&& isset($_POST['levelid'])
&& isset($_POST['yearid'])
&& isset($_POST['periodid'])
&& isset($_POST['courseid'])
&& isset($_POST['modal_comp_name'])
&& isset($_POST['modal_comp_code'])
&& isset($_POST['modal_comp_desc'])
&& isset($_POST['modal_comp_percent'])
&& isset($_POST['sc'])){

        $userid = isset($_SESSION['EMPLOYEE']['ID']) ? $_SESSION['EMPLOYEE']['ID'] : -1;

        $gscale_name = $_POST['gscale_name'];
        $gscale_id = $_POST['gscale_id'];
        $gscale_code = $_POST['gscale_code'];
        $gscale_desc = $_POST['gscale_desc'];
        $pass_score = $_POST['pass_score'];
        $levelid = $_POST['levelid'];
        $yearid = $_POST['yearid'];
        $periodid = $_POST['periodid'];
        $courseid = $_POST['courseid'];
        $modal_cname = $_POST['modal_comp_name'];
        $modal_ccode = $_POST['modal_comp_code'];
        $modal_cdesc = $_POST['modal_comp_desc'];
        $modal_cpercent = $_POST['modal_comp_percent'];
        $subcomp = $_POST['sc'];
        
        if($gscale_name !== '' && $gscale_code !== '' && $pass_score !== '' && $gscale_id !== ''){
            $status = "FAILED";
            $sql = "UPDATE `schoolacademicgradingscale` `gscale`

                        SET `gscale`.`SchlAcadGradScale_CODE` = '$gscale_code',
                            `gscale`.`SchlAcadGradScale_NAME` = '$gscale_name',
                            `gscale`.`SchlAcadGradScale_DESC`= '$gscale_desc',
                            `gscale`.`SchlAcadGradScale_PASS_SCORE`= $pass_score
                            
                        WHERE `gscale`.`SchlAcadGradScale_ID` = '$gscale_id';"; 
            $result = mysqli_query($dbConn, $sql); 

            if(mysqli_affected_rows($dbConn) > 0){
                $status = "SUCESS";
                $array_sc = explode(':', $subcomp);
                for($i = 0; $i < count($modal_cname); $i++){
                    $comp_name = $modal_cname[$i];
                    $comp_code = $modal_ccode[$i];
                    $comp_desc = $modal_cdesc[$i];
                    $comp_percent = $modal_cpercent[$i];
                    $rank = $i + 1;

                    if($modal_cname[$i] !== '' && $modal_ccode[$i] !== '' 
                    // && $modal_cdesc[$i] !== '' 
                    && $modal_cpercent[$i] !== ''){
                        $sql1 = "UPDATE `schoolacademicgradingscaledetail` `gscaledet`

                                    SET `gscaledet`.`SchlAcadGradScaleDet_CODE` = '$comp_code',
                                        `gscaledet`.`SchlAcadGradScaleDet_DESC` = '$comp_desc',
                                        `gscaledet`.`SchlAcadGradScaleDet_NAME` = '$comp_name',
                                        `gscaledet`.`SchlAcadGradScaleDet_PERCENTAGE` = $comp_percent

                                    WHERE `gscaledet`.`SchlAcadGradScale_ID` = $gscale_id
                                        AND `gscaledet`.`SchlAcadGradScaleDet_PARENT_ID` = 0
                                        AND `gscaledet`.`SchlAcadGradScaleDet_RANKNO` = $rank
                                        ;";
                        if(mysqli_query($dbConn, $sql1)){
                            $array_sc1 = explode("|",$array_sc[$i]);
                            
                            for($j = 0; $j < count($array_sc1)-1; $j++){
                                $array_sc2 = explode(",",$array_sc1[$j]);
                                
                                $parentid = $array_sc2[0];
                                $subcomp_name = $array_sc2[1];
                                $subcomp_code  = $array_sc2[2];
                                $subcomp_desc = $array_sc2[3];
                                $subcomp_percent = $array_sc2[4];
                                $rank_sc = $j + 1;

                                if($subcomp_name !== '' 
                                  && $subcomp_code !== '' 
                                //   && $subcomp_desc !== '' 
                                  && $subcomp_percent !== ''){
                                        // echo '<script>alert("code '.$subcomp_code.' \nname '.$subcomp_name.' \ndesc '.$subcomp_desc.' \npercent '.$subcomp_percent.' \nparentid '.$parentid.' \ngscale_id '.$gscale_id.' \nrank_sc '.$rank_sc.'")</script>';
                                    $sql2 = "UPDATE `schoolacademicgradingscaledetail` `gscaledet`

                                                SET `gscaledet`.`SchlAcadGradScaleDet_CODE` = '$subcomp_code',
                                                    `gscaledet`.`SchlAcadGradScaleDet_DESC` = '$subcomp_desc',
                                                    `gscaledet`.`SchlAcadGradScaleDet_NAME` = '$subcomp_name',
                                                    `gscaledet`.`SchlAcadGradScaleDet_PERCENTAGE` = $subcomp_percent

                                                WHERE `gscaledet`.`SchlAcadGradScale_ID` = $gscale_id
                                                    AND `gscaledet`.`SchlAcadGradScaleDet_PARENT_ID` = $parentid
                                                    AND `gscaledet`.`SchlAcadGradScaleDet_RANKNO` = $rank_sc
                                                    ;";
                  
                                    if(mysqli_query($dbConn, $sql2)){
                                        $status = "SUCCESS";
                                        // echo '<script>alert("SUBCOMPONENT EDIT SUCCESSFUL.")</script>';
                                    } else {
                                        echo '<script>alert("SUBCOMPONENT EDIT UNSUCCESSFUL.")</script>';
                                    }
                  
                                } else {
                                  echo '<script>alert("There are BLANK Subcomponent INPUTS.")</script>';
                  
                                }
                            }
                        } else {
                            echo '<script>alert("COMPONENT EDIT UNSUCCESSFUL.")</script>';
                        }
                        
                    } else {
                        echo '<script>alert("There are BLANK GRADING SCALE COMPONENTS.")</script>';
                    }
                }

                // Do Logging here
                $syntax = json_encode([
                    "status" => $status,
                    "id" => $userid,
                    "gscale_id" => $gscale_id
                ]);

                $module = 'EDIT GRADING SCALE';
                $operation = 'EDITING OF GRADING SCALE';
                
                $stmt = $dbConn->prepare("CALL `spInsertLogs`(?, ?, ?, ?, 'systemuser')");
                $stmt->bind_param("isss", $userid, $module, $operation, $syntax);
                $stmt->execute();
            } else {
                echo '<script>alert("GSCALE INFO EDIT UNSUCCESSFUL.")</script>';
            }
            
        } else {
            echo '<script>alert("There are BLANK GRADING SCALE DETAILS.")</script>';

        }

        
} else {
    echo '<script>alert("Variables are not set. Contact ICT Department.")</script>';
}

    

$dbConn->close();
?>
