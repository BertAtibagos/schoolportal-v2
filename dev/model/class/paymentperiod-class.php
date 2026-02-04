<?php 
	require_once('../configuration/connection-config.php');
	session_start();
    if( isset($_POST['year_id']) && isset($_POST['level_id']))
    {
        $qry ="	SELECT 	DISTINCT
						`SCHL_INV`.`schlacadprd_id` `ACADPRD_ID`,
						`ACAD_PRD`.`schlacadprd_name` `ACADPRD_NAME`
					
				FROM `schoolenrollmentassessment` `SCHL_ASS`
					LEFT JOIN `schoolenrollmentinvoice` `SCHL_INV`
						ON `SCHL_ASS`.`schlenrollasssms_id` = `SCHL_INV`.`schlenrollass_id`
					
					LEFT JOIN `schoolacademiclevel` `ACAD_LVL`
						ON `SCHL_INV`.`schlacadlvl_id` = `ACAD_LVL`.`schlacadlvlsms_id`
						
					LEFT JOIN `schoolacademicyear` `ACAD_YR`
						ON `SCHL_INV`.`schlacadyr_id` = `ACAD_YR`.`schlacadyrsms_id`	
							
					LEFT JOIN `schoolacademicperiod` `ACAD_PRD`
						ON `SCHL_INV`.`schlacadprd_id` = `ACAD_PRD`.`schlacadprdsms_id`

				WHERE 	`SCHL_ASS`.`schlstud_id` =    " . $_SESSION['USERID']. " AND 
						`SCHL_INV`.`schlacadlvl_id` = " . $_POST['level_id'] . " AND
						`SCHL_INV`.`schlacadyr_id` =  " . $_POST['year_id'];



    	$rsreg = $dbConn->query($qry);
    	$fetchacadperiod = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->close();
    	$createDropDown   = "<label>ACADEMIC PERIOD</label>";
		$createDropDown  .= "<select id='pay_acadprd' name='pay_acadprd' class='form-control' style='text-align:center;'>";
		$createDropDown .= "<option value='0'> -- SELECT ACADEMIC PERIOD -- </option>";
		foreach($fetchacadperiod as $regitem)
		{
			$createDropDown .= "<option value='".$regitem['ACADPRD_ID']."'>".$regitem['ACADPRD_NAME']."</option>";
		}
		$createDropDown .= '</select>';
		echo $createDropDown;
	}
?>
<script>
	$(document).ready(function(){

		$('#pay_acadprd').change(function(){

			var period_id = $(this).val();
			var year_id   = $('#pay_acadyr').val();
			var level_id  = $('#pay_acadlvl').val();

			$.ajax({
				type: "POST",
				url: "../../model/class/paymentcourse-class.php",
				data:{
					period_id : period_id,
					year_id : year_id,
					level_id : level_id
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
