<?php 
	session_start();
	include('../configuration/connection-config.php');
    if(isset($_POST['level_id']) && isset($_POST['year_id']))
    {
		
    	$_SESSION['LEVEL_ID'] = $_POST['level_id'];
    	$_SESSION['YEAR_ID'] = $_POST['year_id'];

		$qry ="SELECT DISTINCT  `lvl`.`schlacadlvlsms_id` `LVL_ID`, `ass`.`schlacadyr_id` `ACADYR_ID`, `prd`.`schlacadprd_name` `PRD_NAME`, `prd`.`schlacadprd_id` `PRD_ID`
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

				WHERE `stud`.`schlstudsms_id` = ".$_SESSION['USERID']." AND `ass`.`schlenrollass_status` = 1 AND `acadyr`.`schlacadyrsms_id` = ".$_SESSION['YEAR_ID']." AND `lvl`.`schlacadlvlsms_id` = ".$_SESSION['LEVEL_ID']." AND `type`.`sysusertype_name` =  '".$_SESSION['USERTYPE']."';";

    	$rsreg = $dbConn->query($qry);
    	$fetchacadperiod = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->close();
    	$createDropDown   = "<label>ACADEMIC PERIOD</label>";
		$createDropDown  .= "<select id='subj_acadprd' name='acadprd' class='form-control' style='text-align:center;'>";
		$createDropDown .= "	<option value='0'>-- SELECT ACADEMIC PERIOD --</option>";
		foreach($fetchacadperiod as $regitem)
		{
			$createDropDown .= "<option value='".$regitem['PRD_ID']."'>".$regitem['PRD_NAME']."</option>";
		}
		$createDropDown .= '</select>';
		echo $createDropDown;
	}
?>


<script>
	$(document).ready(function(){

		$('#subj_acadprd').change(function(){

			var period_id = $(this).val();
			var level_id = $('#subj_acadlvl').val();
			var year_id = $('#subj_acadyr').val();

			$.ajax({
				type: "POST",
				url: "../../model/class/schoolenrollmentsubjectschedcourse-class.php",
				data:{
					year_id : year_id,
					level_id : level_id,
					period_id : period_id
				},
				success: function(data)
				{
					$("#dropdown-academic-course").show();
					$("#dropdown-academic-course").html(data);
				}
			});			
		});
	});
</script>