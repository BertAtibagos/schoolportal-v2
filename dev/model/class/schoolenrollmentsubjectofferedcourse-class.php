<?php 
	session_start();
	require_once('../configuration/connection-config.php');

    if( isset($_POST['period_id']) && isset($_POST['year_id']) && isset($_POST['level_id']))
    {
        $qry = "	SELECT DISTINCT	`subj_off`.`schlacadcrses_id`, 
									`acad_crs`.`schlacadcrses_name` 
									
					FROM `schoolenrollmentsubjectoffered` `subj_off`

						LEFT JOIN `schoolacademiccourses` `acad_crs`
							ON `subj_off`.`schlacadcrses_id` = `acad_crs`.`schlacadcrses_id`

					WHERE  	`subj_off`.`schlprof_id` = ". $_SESSION['USERID'] ." AND 
							`subj_off`.`schlacadlvl_id` = " .$_POST['level_id'] . " AND 
							`subj_off`.`schlacadyr_id`  = " .$_POST['year_id'];

    	$rsreg = $dbConn->query($qry);
    	$fetchacadcourse = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->close();

    	$createDropDown  = "<label>ACADEMIC COURSE</label>";
		$createDropDown .= "<select id='cls_acadcrs' name='cls_acadcrs' class='form-control' style='text-align:center;'>";
		$createDropDown .= "	<option value='0'>-- SELECT ACADEMIC COURSE --</option>";

		foreach($fetchacadcourse as $regitem)
		{
			$createDropDown .= "<option value='".$regitem['schlacadcrses_id']."'>".$regitem['schlacadcrses_name']."</option>";
		}
		$createDropDown .= '</select>';
		echo $createDropDown;
	}
?>

<script>
	$(document).ready(function(){

		$('#cls_acadcrs').change(function(){


			var course_id	= $(this).val();
			var period_id   = $('#cls_acadprd').val();
			var year_id 	= $('#cls_acadyr').val();
			var level_id 	= $('#cls_acadlvl').val();

			$.ajax({
				type: "POST",
				url: "../../model/class/classlistcourses-class.php",
				data:{
					course_id : course_id,
					period_id : period_id,
					year_id : year_id,
					level_id : level_id
				},
				success: function(data)
				{
					$("#table-academic-subject").show();
					$("#table-academic-subject").html(data);
				}
			});			
		});
	});

</script>