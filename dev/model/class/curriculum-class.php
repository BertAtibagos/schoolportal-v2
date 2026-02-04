<?php  
	
	// FOR GETTING COURSE NAME

	$qry_currheader = "
			SELECT	
					`ACAD_CRS`.`schlacadcrses_name` `CRSE_NAME`,
					`SCHL_CURR`.`schlacadcurr_code` `CURR_CODE`, 
					`SCHL_CURR`.`schlacadcurr_name` `CURR_NAME`


			FROM `schoolstudent` `SCHL_STUD` 
				LEFT JOIN `schoolenrollmentregistration` `SCHL_REG`
					ON `SCHL_STUD`.`schlenrollregcoll_id` = `SCHL_REG`.`schlenrollregsms_id`
				
				LEFT JOIN `schoolenrollmentregistrationstudentinformation` `SCHL_REGINFO`
					ON `SCHL_REG`.`schlenrollregsms_id` = `SCHL_REGINFO`.`schlenrollregstudinfosms_id`
					
				LEFT JOIN `schoolenrollmentadmission` `SCHL_ADM`
					ON `SCHL_STUD`.`schlenrollregcoll_id` = `SCHL_ADM`.`schlenrolladmsms_id`
				
				LEFT JOIN `schoolenrollmentassessment` `SCHL_ASS`
					ON `SCHL_ADM`.`schlenrolladmsms_id` = `SCHL_ASS`.`schlenrollasssms_id`
					
				LEFT JOIN `schoolacademiccourses` `ACAD_CRS`
					ON `SCHL_ASS`.`schlacadcrse_id` = `ACAD_CRS`.`schlacadcrsesms_id`
					
					
				LEFT JOIN `schoolacademiccurriculum` `SCHL_CURR`
					ON `SCHL_ASS`.`schlacadcurr_id` = `SCHL_CURR`.`schlacadcurrsms_id`

			WHERE 
				`SCHL_STUD`.`schlstud_id` = " . $_SESSION['USERID'];

	$currheader_rsreg = $dbConn->query($qry_currheader);
    $fetchcurrheader  = $currheader_rsreg->fetch_assoc();
	$currheader_rsreg->close();



	// // FOR GETTING ACAD_YRLVL & ACAD_PRD

	// $qry_yrlvl_yrprd = " 
	// 					SELECT	DISTINCT
	// 									`ACAD_LVL`.`schlacadlvl_name` `ACAD_LVL`,
	// 									`ACAD_YRLVL`.`schlacadyrlvl_name` `ACAD_YRLVL_NAME`,
	// 									`ACAD_CURRSUBJ`.`schlacadyrlvl_id` `ACAD_YRLVL_ID`,
	// 									`ACAD_PRD`.`schlacadprd_name` `ACAD_PRD_NAME`,
	// 									`ACAD_CURRSUBJ`.`schlacadprd_id` `ACAD_PRD_ID`

										
	// 					FROM `schoolstudent` `SCHL_STUD` 
	// 						LEFT JOIN `schoolenrollmentregistration` `SCHL_REG`
	// 							ON `SCHL_STUD`.`schlenrollregcoll_id` = `SCHL_REG`.`schlenrollregsms_id`
							
	// 						LEFT JOIN `schoolenrollmentregistrationstudentinformation` `SCHL_REGINFO`
	// 							ON `SCHL_REG`.`schlenrollregsms_id` = `SCHL_REGINFO`.`schlenrollregstudinfosms_id`
								
	// 						LEFT JOIN `schoolenrollmentadmission` `SCHL_ADM`
	// 							ON `SCHL_STUD`.`schlenrollregcoll_id` = `SCHL_ADM`.`schlenrolladmsms_id`
							
	// 						LEFT JOIN `schoolenrollmentassessment` `SCHL_ASS`
	// 							ON `SCHL_ADM`.`schlenrolladmsms_id` = `SCHL_ASS`.`schlenrollasssms_id`
								
	// 						LEFT JOIN `schoolacademiccourses` `ACAD_CRS`
	// 							ON `SCHL_ASS`.`schlacadcrse_id` = `ACAD_CRS`.`schlacadcrsesms_id`
								
	// 						LEFT JOIN `schoolacademiclevel` `ACAD_LVL`
	// 							ON `SCHL_ASS`.`schlacadlvl_id` = `ACAD_LVL`.`schlacadlvlsms_id`
							
	// 						LEFT JOIN `schoolacademiccurriculum` `SCHL_CURR`
	// 							ON `SCHL_ASS`.`schlacadcurr_id` = `SCHL_CURR`.`schlacadcurrsms_id`
							
	// 						LEFT JOIN `schoolacademiccurriculumsubject` `ACAD_CURRSUBJ`
	// 							ON `SCHL_CURR`.`schlacadcurrsms_id` = `ACAD_CURRSUBJ`.`schlacadcurr_id`
							
							
	// 						LEFT JOIN `schoolacademicyearlevel` `ACAD_YRLVL`
	// 							ON `ACAD_CURRSUBJ`.`schlacadyrlvl_id` = `ACAD_YRLVL`.`schlacadyrlvlsms_id`
							
	// 						LEFT JOIN `schoolacademicperiod` `ACAD_PRD` 
	// 							ON `ACAD_CURRSUBJ`.`schlacadprd_id` = `ACAD_PRD`.`schlacadprdsms_id`
							
	// 					WHERE 	
	// 							`SCHL_STUD`.`schlstud_id` = 1 AND 
	// 							`ACAD_CURRSUBJ`.`schlacadlvl_id` = 2
							
	// 					ORDER BY	`ACAD_CURRSUBJ`.`schlacadyrlvl_id`,
	// 								`ACAD_CURRSUBJ`.`schlacadprd_id`";

	// $yrlvl_yrprd_rsreg = $dbConn->query($qry_yrlvl_yrprd);
    // $fetchyrlvl_yrprd  = $yrlvl_yrprd_rsreg->fetch_ALL(MYSQLI_ASSOC);
	// $yrlvl_yrprd_rsreg->close();

	// FOR SHOWING FULL DATA 

	$qry_currsubjects = "	
						SELECT	`ACAD_CURRSUBJ`.`schlacadcurrsubj_id` `SUBJ_ID`,
								`ACAD_CRS`.`schlacadcrses_name` `CRSE_NAME`,
								`SCHL_CURR`.`schlacadcurr_code` `CURR_CODE`, 
								`SCHL_CURR`.`schlacadcurr_name` `CURR_NAME`,
								`ACAD_LVL`.`schlacadlvl_name` `ACAD_LVL`,
								`ACAD_YRLVL`.`schlacadyrlvl_name` `ACAD_YRLVL_NAME`,
								`ACAD_CURRSUBJ`.`schlacadyrlvl_id` `ACAD_YRLVL_ID`,
								`ACAD_PRD`.`schlacadprd_name` `ACAD_PRD_NAME`,
								`ACAD_CURRSUBJ`.`schlacadprd_id` `ACAD_PRD_ID`,
								`ACAD_SUBJ`.`schlacadsubj_code` `SUBJ_CODE`,
								`ACAD_SUBJ`.`schlacadsubj_name` `SUBJ_NAME`,
								`ACAD_SUBJ`.`schlacadsubj_unit` `SUBJ_UNIT`,
								`ACAD_SUBJ`.`schlacadsubj_lec`  `SUBJ_LEC`,
								`ACAD_SUBJ`.`schlacadsubj_lab`  `SUBJ_LAB`

								
						FROM `schoolstudent` `SCHL_STUD` 
							LEFT JOIN `schoolenrollmentregistration` `SCHL_REG`
								ON `SCHL_STUD`.`schlenrollregcoll_id` = `SCHL_REG`.`schlenrollregsms_id`
							
							LEFT JOIN `schoolenrollmentregistrationstudentinformation` `SCHL_REGINFO`
								ON `SCHL_REG`.`schlenrollregsms_id` = `SCHL_REGINFO`.`schlenrollregstudinfosms_id`
								
							LEFT JOIN `schoolenrollmentadmission` `SCHL_ADM`
								ON `SCHL_STUD`.`schlenrollregcoll_id` = `SCHL_ADM`.`schlenrolladmsms_id`
							
							LEFT JOIN `schoolenrollmentassessment` `SCHL_ASS`
								ON `SCHL_ADM`.`schlenrolladmsms_id` = `SCHL_ASS`.`schlenrollasssms_id`
								
							LEFT JOIN `schoolacademiccourses` `ACAD_CRS`
								ON `SCHL_ASS`.`schlacadcrse_id` = `ACAD_CRS`.`schlacadcrsesms_id`
								
							LEFT JOIN `schoolacademiclevel` `ACAD_LVL`
								ON `SCHL_ASS`.`schlacadlvl_id` = `ACAD_LVL`.`schlacadlvlsms_id`
							
							LEFT JOIN `schoolacademiccurriculum` `SCHL_CURR`
								ON `SCHL_ASS`.`schlacadcurr_id` = `SCHL_CURR`.`schlacadcurrsms_id`
							
							LEFT JOIN `schoolacademiccurriculumsubject` `ACAD_CURRSUBJ`
								ON `SCHL_CURR`.`schlacadcurrsms_id` = `ACAD_CURRSUBJ`.`schlacadcurr_id`
							
							LEFT JOIN `schoolacademicsubject` `ACAD_SUBJ`
								ON `ACAD_CURRSUBJ`.`schlacadsubj_id` = `ACAD_SUBJ`.`schlacadsubjsms_id`
								
							LEFT JOIN `schoolacademicyearlevel` `ACAD_YRLVL`
								ON `ACAD_CURRSUBJ`.`schlacadyrlvl_id` = `ACAD_YRLVL`.`schlacadyrlvlsms_id`
							
							LEFT JOIN `schoolacademicperiod` `ACAD_PRD` 
								ON `ACAD_CURRSUBJ`.`schlacadprd_id` = `ACAD_PRD`.`schlacadprdsms_id`
							
						WHERE 	`ACAD_SUBJ`.`schlacadsubj_status` = 1 AND
								`ACAD_SUBJ`.`schlacadsubj_isactive` = 1 AND
								`SCHL_STUD`.`schlstud_id` = " . $_SESSION['USERID'] . " AND 
								`ACAD_CURRSUBJ`.`schlacadlvl_id` = " . $_SESSION['LVLID'] . "
							
						ORDER BY	`ACAD_CURRSUBJ`.`schlacadyrlvl_id`,
									`ACAD_CURRSUBJ`.`schlacadprd_id` ";

	$currsubject_rsreg = $dbConn->query($qry_currsubjects);
    $fetchcurrsubjects = $currsubject_rsreg->fetch_ALL(MYSQLI_ASSOC);

	$currsubject_rsreg->close();

	// print_r($fetchcurrsubjects[0]);



?>