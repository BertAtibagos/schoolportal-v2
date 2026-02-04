<?php 
	require_once('../configuration/connection-config.php');
    if(isset($_POST['level_id']))
    {
        $qry = "SELECT *
				FROM `schoolacademiccourses`
				WHERE 	`schlacadlvl_id` = ".$_POST['level_id']." AND
						`schlacadcrses_status` = 1 AND
						`schlacadcrses_isactive` = 1 ";

    	$rsreg = $dbConn->query($qry);
    	$fetchacadcourse = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->close();
    	$createDropDownCourse   = "<label>ACADEMIC COURSE</label>";
		$createDropDownCourse  .= "<select id='acadcrs' name='acadcrs' class='form-control' style='text-align:center;'>";
		foreach($fetchacadcourse as $regitem)
		{
			$createDropDownCourse .= "<option value='".$regitem['schlacadcrses_id']."'>".$regitem['schlacadcrses_name']."</option>";
		}

		$createDropDownCourse .= '</select>';
	
		echo $createDropDownCourse;
	}
?>


