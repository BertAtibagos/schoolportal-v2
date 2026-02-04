<?php
	require_once 'configuration/connection-config.php';
    session_start();
    if(isset($_GET['RUN'])) {
        $qry = "SELECT 
                    `yrprd`.`SchlAcadLvl_ID` `LVL_ID`,
                    `yrprd`.`SchlAcadYr_ID` `YR_ID`,
                    `yrprd`.`SchlAcadPrd_ID` `PRD_ID`,
                    `lvl`.`SchlAcadLvl_NAME` `LVL_NAME`,
                    `yr`.`SchlAcadYr_NAME` `YR_NAME`,
                    `prd`.`SchlAcadPrd_NAME` `PRD_NAME`
                    
                    
                    FROM `schoolacademicyearperiod` `yrprd`
                    
                    LEFT JOIN `schoolacademiclevel` `lvl`
                    ON `yrprd`.`SchlAcadLvl_ID` = `lvl`.`SchlAcadLvlSms_ID`
                    LEFT JOIN `schoolacademicyear` `yr`
                    ON `yrprd`.`SchlAcadYr_ID` = `yr`.`SchlAcadYrSms_ID`
                    LEFT JOIN `schoolacademicperiod` `prd`
                    ON `yrprd`.`SchlAcadPrd_ID` = `prd`.`SchlAcadPrdSms_ID`
                    
                    WHERE `yrprd`.`SchlAcadYrPrd_ISOPEN` = 1
                ";
        $rsreg = $dbConn->query($qry);
        $fetch = $rsreg->fetch_ALL(MYSQLI_ASSOC);
        $rsreg->free_result();
        $dbConn->close();
        echo json_encode($fetch);

    } else if (isset($_GET['CHECK_REG_ADM_ASS'])){
        $numberArray = $_GET['array'];
        $count_cont = 0;

        foreach ($numberArray as $row) {
            $qry = "SELECT 
                (	
                    SELECT COUNT(*) AS COUNT

                    FROM `schoolenrollmentregistration` 

                    LEFT JOIN `schoolenrollmentadmission`
                    ON `schoolenrollmentregistration`.`SchlEnrollRegSms_ID` = `schoolenrollmentadmission`.`SchlEnrollReg_ID`
                    LEFT JOIN `schoolenrollmentassessment`
                    ON `schoolenrollmentadmission`.`SchlEnrollAdmSms_ID` = `schoolenrollmentassessment`.`SchlEnrollAdm_ID`

                    WHERE `schoolenrollmentregistration`.`SchlStud_ID` = ".$_SESSION['USERID']." 

                    AND `schoolenrollmentregistration`.`SchlAcadLvl_ID` = ".$row[0]."
                    AND `schoolenrollmentregistration`.`SchlAcadYr_ID` = ".$row[1]."
                    AND `schoolenrollmentregistration`.`SchlAcadPrd_ID` = ".$row[2]."
                ) `COUNT`
            ";
            
            $rsreg = $dbConn->query($qry);
            if($rsreg){
                // Fetch the result row
                $row = $rsreg->fetch_assoc();

                // Access the value of the column and assign it to a variable
                $value = $row['COUNT'];

                // Use the value as needed
                $count_cont = $count_cont + $value;
            }
        }
        echo $count_cont;
        $rsreg->free_result();
        $dbConn->close();

    }
?>
