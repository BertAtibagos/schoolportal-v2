<div class="mt-4 bg-primary bg-opacity-25 border border-primary border-3 rounded p-2">
    <?php
        // echo $_SERVER['REQUEST_URI']; // get current file location
        require_once 'configuration/connection-config.php';
        
        $stmt = $dbConn->prepare("SELECT 
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
                
                WHERE `yrprd`.`SchlAcadYrPrd_ISOPEN` = 1");
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result

        if ($result->num_rows > 0) {
            $year = [];
            $open_same = '';
            $open_diff = '';
            while ($row = $result->fetch_assoc()){ //fetch data
                $year[] = str_replace(' ', '', $row["YR_NAME"]);

                $formattedLevel = ucwords(strtolower($row['LVL_NAME']));
                $formattedPeriod = ucwords(strtolower($row['PRD_NAME']));
                
                $open_same .= "<p class='mb-0'>{$formattedLevel} [{$formattedPeriod}]</p>";
                $open_diff .= "<p class='mb-0'>{$formattedLevel} - {$formattedPeriod} [" . implode(' - ', explode('-', $row["YR_NAME"])) . "]</p>";
            }

            if (!empty($year) && count(array_unique($year)) === 1) { // if all year names are the same
                echo "<h3 class='fw-bold'>Enrollment Ongoing for " . implode(' - ', explode('-', $year[0])) . "!</h3>";
                echo $open_same;
            } else {
                echo "<h3 class='fw-bold'>Enrollment Ongoing:</h3>";
                echo $open_diff;
            }
        } else {
            echo 'No enrollment ongoing.';
            exit();
        }

        echo "<h4 class='mt-4 text-decoration-underline'>Enroll Now!</h4>";

        if(str_contains($_SERVER['REQUEST_URI'], "login-model")){
            // BUTTONS WHEN YOU'RE IN LOGIN PAGE
            echo "
            <div class='row'>
                <div class='col-lg-1'>
                </div>
                <div class='col-lg-5 mb-2 text-end'>
                    <input type='button' value='Old Student' id='btnOldStudent' class='btn btn-warning text-white w-100'>
                </div>
                <div class='col-lg-5 mb-2 text-start'>
                    <a href='../onlineregistration/'  target='_blank'>
                        <input type='button' value='New Student' id='btnNewStudent' class='btn btn-warning text-white w-100'>
                    </a>
                </div>
                <div class='col-lg-1'>
                </div>
            </div>
            ";
        } else if(str_contains($_SERVER['REQUEST_URI'], "home-model")){
            // BUTTONS WHEN YOU'RE IN HOME PAGE
            echo "
            <div class='row'>
                <div class='col-lg mb-2 text-center'>
                    <a href='../../onlineregistration/online-campus/php/partials/main-registration.php' target='_blank'>
                        <input type='button' value='Old Student' id='btnOldStudent_Home' class='btn btn-primary text-white w-25'>
                    </a>
                </div>
            </div>
            ";
        } else {
            exit();
        }
    ?>
</div>