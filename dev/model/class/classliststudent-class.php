<?php 
	session_start();
	require_once('../configuration/connection-config.php');


	// if( isset($_POST['course_code']) && isset($_POST['course_id']) )

    

    if( isset($_POST['subject_id']) && isset($_POST['course_id']) && isset($_POST['period_id']) && isset($_POST['year_id']) && isset($_POST['level_id']) )

    {
    	// echo $_POST['subject_id'];

    	$qry = "	SELECT 	`acad_subj`.`schlacadsubj_id` `SUBJ_ID`,
							`subj_off`.`schlenrollsubjoff_schedule` `SUBJ_SCHED`,
							`acad_subj`.`schlacadsubj_code` `SUBJ_CODE`,
							`acad_subj`.`schlacadsubj_desc` `SUBJ_DESC`,
							`acad_subj`.`schlacadsubj_unit` `SUBJ_UNIT`,
							CONCAT(`acad_yr`.`schlacadyr_name`, '(', `acad_prd`.`schlacadprd_name` , ')') `ACAD_YR/PRD`,
							`acad_sec`.`schlacadsec_code` `SEC`,
							`acad_crs`.`schlacadcrses_code` `CRSES`,
							CONCAT(`emp`.`schlemp_lname`, ', ', `emp`.`schlemp_fname`) `SUBJ_PROF`

					FROM `schoolenrollmentsubjectoffered` `subj_off`
						LEFT JOIN `schoolacademicsubject` `acad_subj`
							ON `subj_off`.`schlacadsubj_id` = `acad_subj`.`schlacadsubjsms_id`
							
						LEFT JOIN `schoolacademicsection` `acad_sec`
							ON `subj_off`.`schlacadsec_id` = `acad_sec`.`schlacadsecsms_id`
							
						LEFT JOIN  `schoolacademiccourses` `acad_crs`
							ON `subj_off`.`schlacadcrses_id` = `acad_crs`.`schlacadcrsesms_id`
							
						LEFT JOIN `schoolemployee` `emp`
							ON `subj_off`.`schlemp_id` = `emp`.`schlemp_id`
							
						LEFT JOIN `schoolacademiclevel` `acad_lvl`
							ON `subj_off`.`schlacadlvl_id` = `acad_lvl`.`schlacadlvl_id`
							
						LEFT JOIN `schoolacademicyear` `acad_yr`
							ON `subj_off`.`schlacadyr_id` = `acad_yr`.`schlacadyr_id`
						
						LEFT JOIN `schoolacademicperiod` `acad_prd`
							ON `subj_off`.`schlacadprd_id` = `acad_prd`.`schlacadprd_id`

					WHERE  	`subj_off`.`schlacadlvl_id` = " . $_POST['level_id']  . " AND 
							`subj_off`.`schlacadyr_id`  = " . $_POST['year_id']   . " AND
							`subj_off`.`schlacadprd_id` = " . $_POST['period_id'] . " AND
							`subj_off`.`schlacadcrses_id` = " . $_POST['course_id'] . " AND 
							`subj_off`.`schlacadsubj_id`  = " . $_POST['subject_id'];
							

    	$rsreg = $dbConn->query($qry);
    	$fetchacadcourse = $rsreg->fetch_assoc();

    	$createDropDown  	 = "<table id='regtable' class='table table-hover table-responsive table-bordered' >";
		$createDropDown  	.= "	<tbody>";
		$createDropDown 	.= "		<tr>";
		$createDropDown 	.= "			<td style='text-align:left;'><label type='label'> <b> ACADEMIC YEAR / PERIOD:</b> </label></td>";
		$createDropDown 	.= "			<td style='text-align:left;'><label type='label'> " . $fetchacadcourse['ACAD_YR/PRD'] . " </label></td>";
		$createDropDown 	.= "		</tr>";
		$createDropDown 	.= "		<tr>";
		$createDropDown 	.= "			<td style='text-align:left;'><label type='label'> <b> SUBJECT CODE:</b> </label></td>";
		$createDropDown 	.= "			<td style='text-align:left;'><label type='label'> " . $fetchacadcourse['SUBJ_CODE'] . " </label></td>";
		$createDropDown 	.= "		</tr>";
		$createDropDown 	.= "		<tr>";
		$createDropDown 	.= "			<td style='text-align:left;'><label type='label'> <b> SUBJECT DESCRIPTION:</b> </label></td>";
		$createDropDown 	.= "			<td style='text-align:left;'><label type='label'> " . $fetchacadcourse['SUBJ_DESC'] . " </label></td>";
		$createDropDown 	.= "		</tr>";
		$createDropDown 	.= "		<tr>";
		$createDropDown 	.= "			<td style='text-align:left;'><label type='label'> <b> SUBJECT UNIT:</b> </label></td>";
		$createDropDown 	.= "			<td style='text-align:left;'><label type='label'> " . $fetchacadcourse['SUBJ_UNIT'] . " </label></td>";
		$createDropDown 	.= "		</tr>";
		$createDropDown 	.= "		<tr>";
		$createDropDown 	.= "			<td style='text-align:left;'><label type='label'> <b> SECTION:</b> </label></td>";
		$createDropDown 	.= "			<td style='text-align:left;'><label type='label'> " . $fetchacadcourse['SEC'] . " </label></td>";
		$createDropDown 	.= "		</tr>";
		$createDropDown 	.= "		<tr>";
		$createDropDown 	.= "			<td style='text-align:left;'><label type='label'> <b> INSTRUCTOR/PROFESSOR:</b> </label></td>";
		$createDropDown 	.= "			<td style='text-align:left;'><label type='label'> " . $fetchacadcourse['SUBJ_PROF'] . " </label></td>";
		$createDropDown 	.= "		</tr>";
		$createDropDown 	.= "		<tr>";
		$createDropDown 	.= "			<td style='text-align:left;'><label type='label'> <b> SCHEDULE:</b> </label></td>";
		$createDropDown 	.= "			<td style='text-align:left;'><label type='label'> " . $fetchacadcourse['SUBJ_SCHED'] . " </label></td>";
		$createDropDown 	.= "		</tr>";
		$createDropDown 	.= "	</tbody>";
		$createDropDown 	.= "</table>";

		echo $createDropDown;	



		$stud_filter_qry =  "	SELECT  `ass`.`schlenrollasssms_id` `assSMS_id`,
								`ass`.`schlacadsubj_id` `assSUBJ_id`,
								`ass`.`schlstud_id` `assSTUD_id`
							FROM `schoolenrollmentassessment` `ass`
							WHERE   `schlacadlvl_id` =   " . $_POST['level_id']  . " AND 
									`schlacadyr_id`  =   " . $_POST['year_id']   . " AND
									`schlacadprd_id` =   " . $_POST['period_id'] . " AND
									`schlacadcrse_id` =  " . $_POST['course_id'] . " AND 
									`schlacadsubj_id` LIKE '%" . $_POST['subject_id'] . "%'";

		$studrsreg = $dbConn->query($stud_filter_qry);
    	$fetchsubjfilter = $studrsreg->fetch_ALL(MYSQLI_ASSOC);

    	//print_r($fetchsubjfilter);
    	$subject_holder_arr = array();

    	foreach($fetchsubjfilter as $subjfilter)
    	{
    		$subjid_arr = explode(",", $subjfilter['assSUBJ_id']);

    		//print_r($subjid_arr);

	    	

	    	$count = 0;
	    	foreach($subjid_arr  as $regitem)
	    	{	

		   		if($regitem == $_POST['subject_id'])
	    		{
	    			array_push($subject_holder_arr, $subjfilter['assSMS_id']);
	    		}
    		
    		}

    		//print_r($subject_holder_arr);

    	}




    	$createDropDown  	 = "<table id='regtable' class='table table-hover table-responsive table-bordered' >";
		$createDropDown  	.= "	<thead class='table table-primary'>";
		$createDropDown  	.= "			<th scope='col' style='text-align:center;'>#</th>";
		$createDropDown 	.= "			<th scope='col' style='text-align:center;'>Name</th>";
		$createDropDown 	.= "			<th scope='col' style='text-align:center;'>Gender</th>";
		$createDropDown 	.= "			<th scope='col' style='text-align:center;'>Course</th>";
		$createDropDown 	.= "			<th scope='col' style='text-align:center;'>Section</th>";
		$createDropDown 	.= "			<th scope='col' style='text-align:center;'>Year Level</th>";
		$createDropDown 	.= "	</thead>";
		$createDropDown 	.= "	<tbody>";

			$count = 1;


				foreach($subject_holder_arr as $stud_info)
	    		{
		    		$qry_students = "	SELECT 	`SHCL_ASS`.`schlstud_id` `STUD_ID`, 
											CONCAT(	`SCHL_REG`.`schlenrollregstudinfo_last_name`,', ',
											`SCHL_REG`.`schlenrollregstudinfo_first_name`, ' ' ,
											`SCHL_REG`.`schlenrollregstudinfo_middle_name`,' ' ,
											`SCHL_REG`.`schlenrollregstudinfo_suffix_name`) `USER_NAME`,
											`SCHL_REG`.`schlenrollregstudinfo_gender` `GENDER`,
											`ACAD_CRS`.`schlacadcrses_code` `ACAD_CRS`,
											`ACAD_SEC`.`schlacadsec_name` `ACAD_SEC`,
											`ACAD_YRLVL`.`schlacadyrlvl_name` `ACAD_YRLVL`
										

									FROM `schoolenrollmentassessment` `SHCL_ASS`	
										LEFT JOIN `schoolenrollmentregistrationstudentinformation` `SCHL_REG`
											ON `SHCL_ASS`.`schlstud_id` = `SCHL_REG`.`schlenrollregstudinfosms_id` 
													
										LEFT JOIN `schoolacademiccourses` `ACAD_CRS`
											ON `SHCL_ASS`.`schlacadcrse_id` = `ACAD_CRS`.`schlacadcrsesms_id`
										
										LEFT JOIN `schoolacademicsection` `ACAD_SEC`
											ON `SHCL_ASS`.`schlacadsec_id` = `ACAD_SEC`.`schlacadsecsms_id`
											
										LEFT JOIN `schoolacademicyearlevel` `ACAD_YRLVL`
											ON `SHCL_ASS`.`schlacadyr_id` = `ACAD_YRLVL`.`schlacadyrlvlsms_id`
								
									WHERE 	`SHCL_ASS`.`schlenrollasssms_id` = " . $stud_info . " AND
											`SHCL_ASS`.`schlacadlvl_id` = " . $_POST['level_id'] . " AND 
											`SHCL_ASS`.`schlacadyr_id` = ". $_POST['year_id'] ." AND
											`SHCL_ASS`.`schlacadprd_id` = " . $_POST['period_id'] . " AND
											`SHCL_ASS`.`schlacadcrse_id` = " . $_POST['course_id'];


					$studrsreg = $dbConn->query($qry_students);
			    	$fetchsubjstudents = $studrsreg->fetch_ALL(MYSQLI_ASSOC);


			    	foreach ($fetchsubjstudents as $regitem)
			    	{
			    		$createDropDown  	.= " <tr> ";
						$createDropDown  	.= "	<td style='text-align:center;'><label type='label'> " . $count++  . " </label></td> ";
						$createDropDown  	.= "	<td style='text-align:center;'><label type='label'> " . $regitem['USER_NAME'] . "</label></td> ";
						$createDropDown  	.= "	<td style='text-align:center;'><label type='label'> " . $regitem['GENDER'] . " </label></td> ";
						$createDropDown  	.= "	<td style='text-align:center;'><label type='label'> " . $regitem['ACAD_CRS'] . " </label></td> ";
						$createDropDown  	.= "	<td style='text-align:center;'><label type='label'> " . $regitem['ACAD_SEC'] . " </label></td> ";
						$createDropDown  	.= "	<td style='text-align:center;'><label type='label'> " . $regitem['ACAD_YRLVL'] . " </label></td> ";
						$createDropDown  	.= " </tr> ";
			    	}


			    	// PRINT_R($fetchsubjstudents);
			    	
	    		}
			



		$createDropDown 	.= "	</tbody>";
		$createDropDown 	.= "</table>";


    	echo $createDropDown;






    	// foreach($fetchsubjstudents  as $regitem)
    	// {
    	// 	$subjid_arr = explode(",", $fetchsubjstudents['schlacadsubj_id']);
    	// 	foreach($subjid_arr as $subj)
    	// 	{
    	// 		if($_POST['subject_id'] == (int)$subj)
    	// 		{
    	// 			echo 1;
    	// 		}
    	// 	}
    	// }


    	// foreach($fetchsubjstudents  as $regitem)
    	// {
    	// 	 $subjid_arr = explode (",", (int)$regitem['schlacadsubj_id']);
    	// 	 foreach ( $subjid_arr as $subj_id) 
    	// 	 {
    	// 	 	if($subj_id == $_POST['subject_id'])
    	// 	 	{
    	// 	 		$subj = array($regitem['schlenrollasssms_id']);
    	// 	 	}
    	// 	 }
    	// }
    }
		

				
	
?>
