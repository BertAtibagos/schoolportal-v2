<?php 
	session_start();
	include('../configuration/connection-config.php');
    if(isset($_POST['level_id']))
    {

    	$_SESSION['LEVEL_ID'] = $_POST['level_id'];

        $qry ="	SELECT *
						FROM `schoolacademicyear` `ACAD_YR`
						WHERE 	`ACAD_YR`.`schlacadlvl_id` = ".$_POST['level_id']." AND `ACAD_YR`.`schlacadyr_status` = 1 AND `ACAD_YR`.`schlacadyr_isactive` = 1 ";

    	$rsreg = $dbConn->query($qry);
    	$fetchacadyear = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->close();
    	$createDropDown   = "<label>ACADEMIC YEAR</label>";
		$createDropDown  .= "<select id='acadyr' name='acadyr' class='form-control' style='text-align:center;'>";
		foreach($fetchacadyear as $regitem)
		{
			$createDropDown .= "<option value='".$regitem['schlacadyr_id']."'>".$regitem['schlacadyr_name']."</option>";
		}
		$createDropDown .= '</select>';
		echo $createDropDown;
	}
?>



