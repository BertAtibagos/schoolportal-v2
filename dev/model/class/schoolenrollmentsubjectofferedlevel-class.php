<?php 
	session_start();
	include '../../../configuration/connection-config.php';
	$qry = " 	SELECT DISTINCT `subj_off`.`schlacadlvl_id`, 
							 	`acad_lvl`.`schlacadlvl_name`
				FROM `schoolenrollmentsubjectoffered` `subj_off`
					LEFT JOIN `schoolacademiclevel` `acad_lvl`
						ON `subj_off`.`schlacadlvl_id` = `acad_lvl`.`schlacadlvl_id` 

				WHERE `subj_off`.`schlprof_id` = " . $_SESSION['USERID'];

   	$rsreg = $dbConn->query($qry);
	$fetchacadlevel = $rsreg->fetch_ALL(MYSQLI_ASSOC);	
	$rsreg->free_result();
	$dbConn->close();
	echo json_encode($fetchacadlevel);
	//echo $fetchacadlevel;
?>
