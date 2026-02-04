<?php 
$qry = 	"	SELECT	DISTINCT
					`SCHL_ASS`.`schlacadlvl_id` `ACADLVL_ID`,
					`ACAD_LVL`.`schlacadlvl_NAME` `ACADLVL_NAME`
				
			FROM `schoolenrollmentassessment` `SCHL_ASS`
				LEFT JOIN `schoolenrollmentinvoice` `SCHL_INV`
					ON `SCHL_ASS`.`schlenrollasssms_id` = `SCHL_INV`.`schlenrollass_id`
				
				LEFT JOIN `schoolacademiclevel` `ACAD_LVL`
						ON `SCHL_INV`.`schlacadlvl_id` = `ACAD_LVL`.`schlacadlvlsms_id`

			WHERE `SCHL_ASS`.`schlstud_id` = " . $_SESSION['USERID'];

   	$rsreg = $dbConn->query($qry);
	$fetchacadlevel = $rsreg->fetch_ALL(MYSQLI_ASSOC);	
?>

