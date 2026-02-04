<?php 
	$qry = "SELECT DISTINCT `lvl`.`schlacadlvl_name` `LVL_NAME`, 
                            `lvl`.`schlacadlvlsms_id` `LVL_ID`, 
                            `ass`.`schlacadsubj_id` `SUBJ`
                            
            FROM systemuser `user`

                LEFT JOIN `schoolstudent` `stud`
                    ON `user`.`schluser_id` = `stud`.`schlstudsms_id`
                LEFT JOIN `schoolenrollmentassessment` `ass`
                    ON `stud`.`schlstudsms_id` = `ass`.`schlstud_id`
                LEFT JOIN `schoolacademiclevel` `lvl`
                    ON `ass`.`schlacadlvl_id` = `lvl`.`schlacadlvlsms_id`
                LEFT JOIN `systemusertype` `type`

                    ON `user`.`sysusertype_id` = `type`.`sysusertypesms_id`
            WHERE   `stud`.`schlstudsms_id` = ".$_SESSION['USERID']." AND 
                    `ass`.`schlenrollass_status` = 1 AND 
                    `type`.`sysusertype_name` =  '".$_SESSION['USERTYPE']."';";

   	$rsreg = $dbConn->query($qry);
	$fetchacadlevel = $rsreg->fetch_ALL(MYSQLI_ASSOC);	
?>

