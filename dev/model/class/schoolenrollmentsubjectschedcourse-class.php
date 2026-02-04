<?php 
	session_start();
	require_once('../configuration/connection-config.php');
    if(isset($_POST['level_id']) && isset($_POST['year_id']) && isset($_POST['year_id']))
    {
    	$_SESSION['LEVEL_ID'] = $_POST['level_id'];
    	$_SESSION['YEAR_ID'] = $_POST['year_id'];
    	$_SESSION['PERIOD_ID'] = $_POST['period_id'];

        $qry = "SELECT DISTINCT  `lvl`.`schlacadlvlsms_id` `LVL_ID`, `ass`.`schlacadyr_id` `ACADYR_ID`, `prd`.`schlacadprd_id` `PRD_ID`, `crse`.`schlacadcrses_name` `CRSE_NAME`, `crse`.`schlacadcrses_id` `CRSE_ID`
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

					WHERE `stud`.`schlstudsms_id` = ".$_SESSION['USERID']." AND `ass`.`schlenrollass_status` = 1 AND `lvl`.`schlacadlvlsms_id` = ".$_SESSION['LEVEL_ID']." AND `acadyr`.`schlacadyrsms_id` = ".$_SESSION['YEAR_ID']." AND `prd`.`schlacadprdsms_id` = ".$_SESSION['PERIOD_ID']." AND `type`.`sysusertype_name` =  '".$_SESSION['USERTYPE']."';";


    	$rsreg = $dbConn->query($qry);
    	$fetchacadcourse = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->close();
    	$createDropDownCourse   = "<label>ACADEMIC COURSE</label>";
		$createDropDownCourse  .= "<select id='subj_acadcrse' name='acadcrse' class='form-control' style='text-align:center;'>";
		$createDropDownCourse .= "	<option value='0'>-- SELECT ACADEMIC COURSE --</option>";
		foreach($fetchacadcourse as $regitem)
		{
			$createDropDownCourse .= "<option value='".$regitem['CRSE_ID']."'>".$regitem['CRSE_NAME']."</option>";
		}

		$createDropDownCourse .= '</select>';
	
		echo $createDropDownCourse;
	}
?>

<script>
	$(document).ready(function(){

		$('#subj_acadcrse').change(function(){
			var crse_id = $(this).val();
			var period_id = $('#subj_acadprd').val();
			var level_id = $('#subj_acadlvl').val();
			var year_id = $('#subj_acadyr').val();

			$.ajax({
				type: "POST",
				url: "../../model/class/subjectschedsubjectlist-class.php",
				data:{
					year_id : year_id,
					level_id : level_id,
					period_id : period_id,
					crse_id : crse_id
				},
				success: function(data)
				{
					$("#prereg-data").show();
					$("#prereg-data").html(data);
				}
			});			
		});
	});
</script>

