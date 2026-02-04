<?php 
	require_once('../configuration/connection-config.php');
    if(isset($_POST['level_id']))
    {
        $qry ="	SELECT *
					FROM `schoolacademicperiod` 
					WHERE `schlacadlvl_id` = " . $_POST['level_id'];
    	$rsreg = $dbConn->query($qry);
    	$fetchacadperiod = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->close();
    	$createDropDown   = "<label>ACADEMIC PERIOD</label>";
		$createDropDown  .= "<select id='acadprd' name='acadprd' class='form-control' style='text-align:center;'>";
		$createDropDown  .= "<option value='0'> -- SELECT ACADEMIC YEAR -- </option>";
		foreach($fetchacadperiod as $regitem)
		{
			$createDropDown .= "<option value='".$regitem['schlacadprd_id']."'>".$regitem['schlacadprd_name']."</option>";
		}
		$createDropDown .= '</select>';
		echo $createDropDown;
	}
?>