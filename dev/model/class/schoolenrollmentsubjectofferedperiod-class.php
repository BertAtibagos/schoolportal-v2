<?php 
	session_start();
	require_once('../configuration/connection-config.php');

    if(isset($_POST['year_id']) && isset($_POST['level_id']))
    {
        $qry = "	SELECT DISTINCT	`subj_off`.`schlacadprd_id`, 
									`acad_prd`.`schlacadprd_name` 
		
					FROM `schoolenrollmentsubjectoffered` `subj_off`
						LEFT JOIN `schoolacademicperiod` `acad_prd`
							ON `subj_off`.`schlacadprd_id` = `acad_prd`.`schlacadprd_id`

					WHERE  	`subj_off`.`schlprof_id` = ". $_SESSION['USERID'] ." AND 
							`subj_off`.`schlacadlvl_id` = " .$_POST['level_id'] . " AND 
							`subj_off`.`schlacadyr_id`  = " .$_POST['year_id'];

    	$rsreg = $dbConn->query($qry);
    	$fetchacadperiod = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->close();

    	$createDropDown   = "<label>ACADEMIC PERIOD</label>";
		$createDropDown  .= "<select id='cls_acadprd' name='cls_acadprd' class='form-control' style='text-align:center;'>";
		$createDropDown .= "	<option value='0'>-- SELECT ACADEMIC PERIOD --</option>";

		foreach($fetchacadperiod as $regitem)
		{
			$createDropDown .= "<option value='".$regitem['schlacadprd_id']."'>".$regitem['schlacadprd_name']."</option>";
		}
		$createDropDown .= '</select>';
		echo $createDropDown;
	}
?>
<script>
	$(document).ready(function(){

		$('#cls_acadprd').change(function(){

			var period_id = $(this).val();
			var year_id = $('#cls_acadyr').val();
			var level_id = $('#cls_acadlvl').val();

			$.ajax({
				type: "POST",
				url: "../../model/class/schoolenrollmentsubjectofferedcourse-class.php",
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

