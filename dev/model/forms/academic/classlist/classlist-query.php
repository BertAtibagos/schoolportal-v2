<?php
	$qryreg = "SELECT 	`acad_subj`.`schlacadsubj_id` `SUBJ_ID`,
						`subj_off`.`schlenrollsubjoff_schedule` `SUBJ_SCHED`,
						`acad_subj`.`schlacadsubj_code` `SUBJ_CODE`,
						`acad_subj`.`schlacadsubj_desc` `SUBJ_DESC`,
						`acad_subj`.`schlacadsubj_unit` `SUBJ_UNIT`,
						CONCAT(`acad_yr`.`schlacadyr_name`, '(', `acad_prd`.`schlacadprd_name` , ')') `ACAD_YR/PRD`,
						`acad_sec`.`schlacadsec_code` `SEC`,
						CONCAT(`emp`.`schlemp_lname`, ', ', `emp`.`schlemp_fname`) `PROF`

				FROM `schoolenrollmentsubjectoffered` `subj_off`
					LEFT JOIN `schoolacademicsubject` `acad_subj`
						ON `subj_off`.`schlacadsubj_id` = `acad_subj`.`schlacadsubjsms_id`
						
					LEFT JOIN `schoolacademicsection` `acad_sec`
						ON `subj_off`.`schlacadsec_id` = `acad_sec`.`schlacadsecsms_id`
						
					LEFT JOIN `schoolemployee` `emp`
						ON `subj_off`.`schlemp_id` = `emp`.`schlemp_id`
						
					LEFT JOIN `schoolacademiclevel` `acad_lvl`
						ON `subj_off`.`schlacadlvl_id` = `acad_lvl`.`schlacadlvl_id`
						
					LEFT JOIN `schoolacademicyear` `acad_yr`
						ON `subj_off`.`schlacadyr_id` = `acad_yr`.`schlacadyr_id`
					
					LEFT JOIN `schoolacademicperiod` `acad_prd`
						ON `subj_off`.`schlacadprd_id` = `acad_prd`.`schlacadprd_id`
					
				WHERE 	
						`subj_off`.`schlprof_id` = " . $_SESSION['SYSUSERSMSID']; 

    $rsreg = $dbConn->query($qryreg);
    $fetchDatareg = $rsreg->fetch_ALL(MYSQLI_ASSOC);


  ?>

