<?php 
	session_start();
	include('../configuration/connection-config.php');
    if(isset($_POST['level_id']))
    {
        $qry ="	SELECT 	DISTINCT
						`SCHL_INV`.`schlacadyr_id` `ACADYR_ID`,
						`ACAD_YR`.`schlacadyr_name` `ACADYR_NAME`
	
				FROM `schoolenrollmentassessment` `SCHL_ASS`
					LEFT JOIN `schoolenrollmentinvoice` `SCHL_INV`
						ON `SCHL_ASS`.`schlenrollasssms_id` = `SCHL_INV`.`schlenrollass_id`
					
					LEFT JOIN `schoolacademiclevel` `ACAD_LVL`
							ON `SCHL_INV`.`schlacadlvl_id` = `ACAD_LVL`.`schlacadlvlsms_id`
						
					LEFT JOIN `schoolacademicyear` `ACAD_YR`
							ON `SCHL_INV`.`schlacadyr_id` = `ACAD_YR`.`schlacadyrsms_id`

				WHERE 	`SCHL_ASS`.`schlstud_id` = " . $_SESSION['USERID'] . " AND 
						`SCHL_INV`.`schlacadlvl_id` = " . $_POST['level_id'];
						

    	$rsreg = $dbConn->query($qry);
    	$fetchacadyear = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->close();
    	$createDropDown   = "<label>ACADEMIC YEAR</label>";
		$createDropDown  .= "<select id='pay_acadyr' name='pay_acadyr' class='form-control' style='text-align:center;'>";
		$createDropDown .= "<option value='0'> -- SELECT ACADEMIC YEAR -- </option>";
		foreach($fetchacadyear as $regitem)
		{
			$createDropDown .= "<option value='".$regitem['ACADYR_ID']."'>".$regitem['ACADYR_NAME']."</option>";
		}
		$createDropDown .= '</select>';
		echo $createDropDown;
	}
?>
<script>
	$(document).ready(function(){

		$('#pay_acadyr').change(function(){

			var year_id = $(this).val();
			var level_id = $('#pay_acadlvl').val();

			$.ajax({
				type: "POST",
				url: "../../model/class/paymentperiod-class.php",
				data:{
					year_id : year_id,
					level_id : level_id
				},
				success: function(data)
				{
					$("#dropdown-academic-period").show();
					$("#dropdown-academic-period").html(data);
				}
			});			
		});
	});

</script>




