<?php 
	session_start();
	include('../configuration/connection-config.php');
    if(isset($_POST['level_id']))
    {
        $qry ="	SELECT DISTINCT	`subj_off`.`schlacadyr_id`, 
								`acad_yr`.`schlacadyr_name` 
		
				FROM `schoolenrollmentsubjectoffered` `subj_off`
					LEFT JOIN `schoolacademiclevel` `acad_lvl`
						ON `subj_off`.`schlacadlvl_id` = `acad_lvl`.`schlacadlvl_id` 
					LEFT JOIN `schoolacademicyear` `acad_yr`
						ON `subj_off`.`schlacadyr_id` = `acad_yr`.`schlacadyr_id`

				WHERE  	`subj_off`.`schlprof_id` = ". $_SESSION['USERID'] ." AND 
	   					`subj_off`.`schlacadlvl_id` = " . $_POST['level_id'] ;
		$rsreg = $dbConn->query($qry);
		$fetchacadyear = $rsreg->fetch_ALL(MYSQLI_ASSOC);	
		// Free result set
		$rsreg->free_result();

		// Close the connection after using it
		$dbConn->close();
		echo json_encode($fetchacadyear);
		echo $createDropDown;
	}
?>
