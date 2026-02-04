	<?php 
	session_start();
	require_once('../configuration/connection-config.php');
    
    if( isset($_POST['crse_id']) && isset($_POST['period_id']) && isset($_POST['year_id']) && isset($_POST['level_id']))
    {
        $sql="SELECT    `crse`.`schlacadcrses_name` `CRSE_NAME`, `sec`.`schlacadsec_name` `SEC_NAME`, `year`.`schlacadyr_name` `YEAR_NAME`,
                        `prd`.`schlacadprd_name` `PRD_NAME`, `yrlvl`.`schlacadyrlvl_desc` `YRLVL_DESC`
                FROM systemuser `user`
                    LEFT JOIN `schoolstudent` `stud`
                    ON `user`.`schluser_id` = `stud`.`schlstudsms_id`
                    LEFT JOIN `schoolenrollmentassessment` `ass`
                    ON `ass`.`schlstud_id` = `stud`.`schlstudsms_id`
                    LEFT JOIN `systemusertype` `type`
                    ON `user`.`sysusertype_id` = `type`.`sysusertypesms_id`
                    
                    LEFT JOIN `schoolacademiccourses` `crse`
                    ON `ass`.`schlacadcrse_id` = `crse`.`schlacadcrsesms_id`
                    LEFT JOIN `schoolacademicsection` `sec`
                    ON `ass`.`schlacadsec_id` = `sec`.`schlacadsecsms_id`
                    LEFT JOIN `schoolacademicyear` `year`
                    ON `ass`.`schlacadyr_id` = `year`.`schlacadyrsms_id`
                    LEFT JOIN `schoolacademicperiod` `prd`
                    ON `ass`.`schlacadprd_id` = `prd`.`schlacadprdsms_id`
                    LEFT JOIN `schoolacademicyearlevel` `yrlvl`
                    ON `ass`.`schlacadyrlvl_id` = `yrlvl`.`schlacadyrlvlsms_id`

                    WHERE `ass`.`schlstud_id` = ".$_SESSION['USERID']." AND 
                    `ass`.`schlacadlvl_id` = ".$_POST['level_id']." AND 
                    `ass`.`schlacadyr_id` = ".$_POST['year_id']." AND 
                    `ass`.`schlacadprd_id` = ".$_POST['period_id']." AND 
                    `ass`.`schlacadcrse_id` = ".$_POST['crse_id']." AND 
                    `ass`.`schlenrollass_status` = 1 AND 
                    `type`.`sysusertypesms_id` =  2 AND 
                    `stud`.`schlenrollregcoll_id` = (
                            SELECT max(`stud`.`schlenrollregcoll_id`) FROM `schoolstudent` `stud` WHERE `ass`.`schlstud_id`= `stud`.`schlstudsms_id`)";
                    
                    $rsreg = $dbConn->query($sql);
	                $fetchacadlevel = $rsreg->fetch_ALL(MYSQLI_ASSOC);	

                    foreach($fetchacadlevel as $regitem){
                        $userData  = "<table class='table table-hover'>";
                        $userData .= "<tbody>";

                        $userData .= "<tr>";
                        $userData .= "<td style='text-align:LEFT;' width='350px'><label type='label' class='text-primary'>" . $regitem['CRSE_NAME'] . "</label></td>";
                        $userData .= "<td style='text-align:center;'><label type='label' class='text-primary'>" . $regitem['YRLVL_DESC'] . "</label></td>";
                        $userData .= "<td style='text-align:center;'><label type='label' class='text-primary'>" . $regitem['SEC_NAME'] . "</label></td>";
                        $userData .= "<td style='text-align:right;'><label type='label' class='text-primary'>" . $regitem['PRD_NAME'] . " (" . $regitem['YEAR_NAME'] . ")" . "</label></td>"; 

                        $userData .= "</tr>";

                        $userData .= "</tbody>";
                        $userData .= "</table>"; 
                    }
                    echo $userData;  

        $qry = "SELECT DISTINCT  `lvl`.`schlacadlvlsms_id` `LVL_ID`, `ass`.`schlacadyr_id` `ACADYR_ID`, `prd`.`schlacadprd_id` `PRD_ID`, `crse`.`schlacadcrses_name` `CRSE_NAME`, `crse`.`schlacadcrses_id` `CRSE_ID`, `ass`.`schlacadsubj_id` `SUBJ`
					FROM systemuser `user`
					LEFT JOIN `schoolstudent` `stud`
					ON `user`.`schluser_id` = `stud`.`schlstudsms_id`
					LEFT JOIN `schoolenrollmentassessment` `ass`
					ON `stud`.`schlstudsms_id` = `ass`.`schlstud_id`
					LEFT JOIN `schoolacademiclevel` `lvl`
					ON `ass`.`schlacadlvl_id` = `lvl`.`schlacadlvlsms_id`
					LEFT JOIN `systemusertype` `type`
					ON `user`.`sysusertype_id` = `type`.`sysusertypesms_id`
					LEFT JOIN `schoolacademicyear` `acadyr`
					ON `ass`.`schlacadyr_id` = `acadyr`.`schlacadyrsms_id`
					LEFT JOIN `schoolacademicperiod` `prd`
					ON `ass`.`schlacadprd_id` = `prd`.`schlacadprdsms_id`
					LEFT JOIN `schoolacademiccourses` `crse`
					ON `ass`.`schlacadcrse_id` = `crse`.`schlacadcrsesms_id`

					WHERE `ass`.`schlstud_id` = ".$_SESSION['USERID']." AND 
                    `ass`.`schlenrollass_status` = 1 AND 
                    `lvl`.`schlacadlvlsms_id` = ".$_SESSION['LEVEL_ID']." AND 
                    `acadyr`.`schlacadyrsms_id` = ".$_SESSION['YEAR_ID']." AND 
                    `prd`.`schlacadprdsms_id` = ".$_SESSION['PERIOD_ID']." AND 
                    `type`.`sysusertype_name` =  '".$_SESSION['USERTYPE']."';";


    	$rsreg = $dbConn->query($qry);
    	$fetchacadcourse = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->close();
		
        $createTable  = "<table id='regtable' class='table table-hover table-responsive table-bordered'>";
        $createTable .= "<thead class='table-primary'>";
        $createTable .= "<tr>";
        $createTable .= "<th scope='col' style='text-align:center;'>#</th>";
        $createTable .= "<th scope='col' style='text-align:center;'>Subject Code</th>";
        $createTable .= "<th scope='col' style='text-align:center;'>Subject Description</th>";
        $createTable .= "<th scope='col' style='text-align:center;'>Unit</th>";
        $createTable .= "<th scope='col' style='text-align:center;'>Section</th>";
        $createTable .= "<th scope='col' style='text-align:center;'>Day/Time</th>";
        $createTable .= "<th scope='col' style='text-align:center;'>Professor</th>";
        // $createTable .= "<th scope='col' style='text-align:center;'class='table-success' >Prelim Grade</th>";
        // $createTable .= "<th scope='col' style='text-align:center;'class='table-success'>Midterm Grade</th>";
        // $createTable .= "<th scope='col' style='text-align:center;'class='table-success'>Finals Grade</th>";
        // $createTable .= "<th scope='col' style='text-align:center;'class='table-success'>GWA</th>";
        $createTable .= "</tr>";
        $createTable .= "</thead>";

        $createTable .= "<tbody>";
        $createTable .= "<tr>";

        foreach($fetchacadcourse as $regitem)
        {
            $subj_id = $regitem['SUBJ'];
        }
        $str_arr = explode (",", $subj_id); 
        foreach ($str_arr as $subject_id) {
            $sql="SELECT    `schlacadsubj_code` `code`,
                            `schlacadsubj_name` `name`,
                            `schlacadsubj_unit` `unit`,
                            `schlenrollsubjoff_schedule_2` `sched`,
                            `schlemp_lname` `lname`,
                            `schlemp_fname` `fname`
                    FROM `schoolenrollmentsubjectoffered` `suboff`
                    LEFT JOIN `schoolacademicsubject` `sub`
                    ON `suboff`.`schlacadsubj_id` = `sub`.`schlacadsubjsms_id`
                    LEFT JOIN `schoolemployee` `emp`
                    ON `suboff`.`schlprof_id` = `emp`.`schlempsms_id`
                    WHERE `suboff`.`schlenrollsubjoff_STATUS` = 1 AND `suboff`.`schlenrollsubjoff_ISACTIVE` = 1 AND `suboff`.`schlenrollsubjoffsms_id` = '".$subject_id."'";
            $rsreg = $dbConn->query($sql);
            while ($row = $rsreg->fetch_assoc()) {
                $createTable .= "<td style='text-align:center;'><label type='label'>" . '1' . "</label></td>";
                $createTable .= "<td style='text-align:center;'><label type='label'>" . $row['code'] . "</label></td>";
                $createTable .= "<td style='text-align:center;'><label type='label'>" . $row['name'] . "</label></td>" ;
                $createTable .= "<td style='text-align:center;'><label type='label'>" . $row['unit'] . ".0 </label></td>" ;
                $createTable .= "<td style='text-align:center;'><label type='label'>" . "IT103" . "</label></td>" ;
                $createTable .= "<td style='text-align:center;'><label type='label'>" . $row['sched'] . "</label></td>" ;
                $createTable .= "<td style='text-align:center;'><label type='label'>" . $row['lname'] . ", " . $row['fname'] . "</label></td>" ;
                // $createTable .= "<td style='text-align:center;'><label type='label'>" . "n/a" . "</label></td>" ;


                $createTable .= "</tr>";

                $createTable .= "</tbody>";
                $createTable .= "<tfooter>";
            }
        }
        $createTable .= "<td style='text-align:center;'></td>";
        $createTable .= "<td style='text-align:center;'></td>";
        $createTable .= "<td style='text-align:center;'></td>";
        $createTable .= "<td style='text-align:center;'><label type='label'>Total Units: " . "15.0" . "</label></td>";
        $createTable .= "</tfooter>";

        $createTable .= "</table>";
        
        echo $createTable;
				
	}
	else
	{
		$createDropDown  	 = "<table id='regtable' class='table table-hover table-responsive table-bordered'>";
		$createDropDown  	.= "	<thead class='table table-primary'>";
		$createDropDown 	.= "		<tr>";
		$createDropDown 	.= "			<th scope='col' style='text-align:center;'>#</th>";
		$createDropDown 	.= "			<th scope='col' style='text-align:center;'>Course Code</th>";
		$createDropDown 	.= "			<th scope='col' style='text-align:center;'>Course Description</th>";
		$createDropDown 	.= "			<th scope='col' style='text-align:center;'>ACTION</th>";
		$createDropDown 	.= "		</tr>";
		$createDropDown 	.= "	</thead>";
		$createDropDown 	.= "</table>";
		echo $createDropDown;
	}
?>

<br><hr><br>

