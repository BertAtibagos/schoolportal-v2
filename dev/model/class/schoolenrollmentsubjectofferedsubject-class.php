<?php 
	session_start();
	require_once('../configuration/connection-config.php');

    if( isset($_POST['course_id']) && isset($_POST['period_id']) && isset($_POST['year_id']) && isset($_POST['level_id']))
    {
        $qry = "	SELECT 	`subj_off`.`schlacadsubj_id`, 
							`acad_subj`.`schlacadsubj_code`, 
							`acad_subj`.`schlacadsubj_name` 
								
					FROM `schoolenrollmentsubjectoffered` `subj_off`

						LEFT JOIN `schoolacademicsubject` `acad_subj`
							ON `subj_off`.`schlacadsubj_id` = `acad_subj`.`schlacadsubj_id`

					WHERE  	`subj_off`.`schlprof_id` = ". $_SESSION['USERID'] ." AND 
							`subj_off`.`schlacadlvl_id` = " . $_POST['level_id'] . " AND 
							`subj_off`.`schlacadyr_id`  = 1 AND
							`subj_off`.`schlacadprd_id` = 1 AND
							`subj_off`.`schlacadprd_id` = 1";


    	$rsreg = $dbConn->query($qry);
    	$fetchacadcourse = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->close();

    	$createDropDown   = "<label>ACADEMIC COURSE</label>";
		$createDropDown  .= "<select id='cls_acadprd' name='cls_acadprd' class='form-control' style='text-align:center;'>";
		$createDropDown .= "	<option value='0'>-- SELECT ACADEMIC COURSE --</option>";

		foreach($fetchacadcourse as $regitem)
		{
			$createDropDown .= "<option value='".$regitem['schlacadcrses_id']."'>".$regitem['schlacadcrses_name']."</option>";
		}
		$createDropDown .= '</select>';
		echo $createDropDown;
	}
?>