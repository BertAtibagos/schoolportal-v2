<?php 
	require_once('../configuration/connection-config.php');
	session_start();

    if( isset($_POST['period_id']) && isset($_POST['year_id']) && isset($_POST['level_id']))
    {
        $qry = "SELECT 	DISTINCT
						`SCHL_ASS`.`schlacadcrse_id` `ACADCRS_ID`,
						`ACAD_CRS`.`schlacadcrses_name` `ACADCRS_NAME`
	
				FROM `schoolenrollmentassessment` `SCHL_ASS`
					LEFT JOIN `schoolenrollmentinvoice` `SCHL_INV`
						ON `SCHL_ASS`.`schlenrollasssms_id` = `SCHL_INV`.`schlenrollass_id`
					
					LEFT JOIN `schoolacademiccourses` `ACAD_CRS`
							ON `SCHL_ASS`.`schlacadcrse_id` = `ACAD_CRS`.`schlacadcrsesms_id`

				WHERE 	`SCHL_ASS`.`schlstud_id` = " .  $_SESSION['USERID'];

    	$rsreg = $dbConn->query($qry);
    	$fetchacadcourse = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->close();

    	$createDropDownCourse  = "<label>ACADEMIC COURSE</label>";
		$createDropDownCourse .= "<select id='pay_acadcrs' name='pay_acadcrs' class='form-control' style='text-align:center;'>";
		$createDropDownCourse .= "<option value='0'> -- SELECT ACADEMIC COURSE -- </option>";
		foreach($fetchacadcourse as $regitem)
		{
			$createDropDownCourse .= "<option value='".$regitem['ACADCRS_ID']."'>".$regitem['ACADCRS_NAME']."</option>";
		}

		$createDropDownCourse .= '</select>';
	
		echo $createDropDownCourse;
	}
?>
<script>
	$(document).ready(function(){

		$('#pay_acadcrs').change(function(){


			var course_id	= $(this).val();
			var period_id   = $('#pay_acadprd').val();
			var year_id 	= $('#pay_acadyr').val();
			var level_id 	= $('#pay_acadlvl').val();


			$.ajax({
				type: "POST",
				url: "../../model/class/payment-class.php",
				data:{
					course_id : course_id,
					period_id : period_id,
					year_id : year_id,
					level_id : level_id
				},
				success: function(data)
				{
					$("#table-payment-transaction").show();
					$("#table-payment-transaction").html(data);
				}
			});			
		});
	});

</script>


