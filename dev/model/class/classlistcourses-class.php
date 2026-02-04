<?php 
	session_start();
	require_once('../configuration/connection-config.php');

    if( isset($_POST['course_id']) && isset($_POST['period_id']) && isset($_POST['year_id']) && isset($_POST['level_id']))
    {
        $qry = "	SELECT 	`acad_subj`.`schlacadsubj_id` `SUBJ_ID`,
							`subj_off`.`schlenrollsubjoff_schedule` `SUBJ_SCHED`,
							`acad_subj`.`schlacadsubj_code` `SUBJ_CODE`,
							`acad_subj`.`schlacadsubj_desc` `SUBJ_DESC`,
							`acad_subj`.`schlacadsubj_unit` `SUBJ_UNIT`,
							CONCAT(`acad_yr`.`schlacadyr_name`, '(', `acad_prd`.`schlacadprd_name` , ')') `ACAD_YR/PRD`,
							`acad_sec`.`schlacadsec_code` `SEC`,
							`acad_crs`.`schlacadcrses_code` `CRSES`,
							CONCAT(`emp`.`schlemp_lname`, ', ', `emp`.`schlemp_fname`) `USERNAME`

					FROM `schoolenrollmentsubjectoffered` `subj_off`
						LEFT JOIN `schoolacademicsubject` `acad_subj`
							ON `subj_off`.`schlacadsubj_id` = `acad_subj`.`schlacadsubjsms_id`
							
						LEFT JOIN `schoolacademicsection` `acad_sec`
							ON `subj_off`.`schlacadsec_id` = `acad_sec`.`schlacadsecsms_id`
							
						LEFT JOIN  `schoolacademiccourses` `acad_crs`
							ON `subj_off`.`schlacadcrses_id` = `acad_crs`.`schlacadcrsesms_id`
							
						LEFT JOIN `schoolemployee` `emp`
							ON `subj_off`.`schlemp_id` = `emp`.`schlemp_id`
							
						LEFT JOIN `schoolacademiclevel` `acad_lvl`
							ON `subj_off`.`schlacadlvl_id` = `acad_lvl`.`schlacadlvl_id`
							
						LEFT JOIN `schoolacademicyear` `acad_yr`
							ON `subj_off`.`schlacadyr_id` = `acad_yr`.`schlacadyr_id`
						
						LEFT JOIN `schoolacademicperiod` `acad_prd`
							ON `subj_off`.`schlacadprd_id` = `acad_prd`.`schlacadprd_id`

					WHERE  	`subj_off`.`schlprof_id`    = " . $_SESSION['USERID'] ." AND 
							`subj_off`.`schlacadlvl_id` = " . $_POST['level_id'] . " AND 
							`subj_off`.`schlacadyr_id`  = " . $_POST['year_id'] . " AND
							`subj_off`.`schlacadprd_id` = " . $_POST['period_id'] . " AND
							`subj_off`.`schlacadcrses_id` = " . $_POST['course_id'] . " AND
							`acad_subj`.`schlacadsubj_status` = 1 AND 
							`acad_subj`.`schlacadsubj_isactive` = 1" ;

    	$rsreg = $dbConn->query($qry);
    	$fetchacadcourse = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->close();






		$createDropDown  	 = "<br><hr><br>";
		$createDropDown  	.= "<table id='regtable' class='table table-hover table-responsive table-bordered'>";
		$createDropDown  	.= "	<thead class='table table-primary'>";
		$createDropDown 	.= "		<tr>";
		$createDropDown 	.= "			<th scope='col' style='text-align:center;'>#</th>";
		$createDropDown 	.= "			<th scope='col' style='text-align:center;'>Course Code</th>";
		$createDropDown 	.= "			<th scope='col' style='text-align:center;'>Course Description</th>";
		$createDropDown 	.= "			<th scope='col' style='text-align:center;'>ACTION</th>";
		$createDropDown 	.= "		</tr>";
		$createDropDown 	.= "	</thead>";

		$createDropDown  	.= "	<tbody>";
		$count = 1;

		foreach($fetchacadcourse as $regitem)
		{
			$createDropDown  .= "<tr>";
			$createDropDown  .= "	<td style='text-align:center;'><label type='label'>". $count++ ."</label></td>";
			$createDropDown  .= "	<td style='text-align:center;'><label type='label'>". $regitem['SUBJ_CODE'] ."</label></td>";
			$createDropDown  .= "	<td style='text-align:center;'><label type='label'>". $regitem['SUBJ_DESC'] ."</label></td>";
			$createDropDown  .= 	"<td style='text-align:center;'><label type='label'><button type='button' class='btn btn-success' value='".$regitem['SUBJ_CODE'].",".$regitem['SUBJ_ID']."'>VIEW CLASS LIST</button></label></td>";

			//$createDropDown  .= 	"<td style='text-align:center;'><label type='label'><button type='button' class='btn btn-success' value = ".$regitem['SUBJ_ID']."> View Class List </button></label></td>";

			$createDropDown  .= "</tr>";

		}



		$createDropDown 	.= "	</tbody>";
		$createDropDown 	.= "</table>";

		echo $createDropDown;

				
	}
?>

    <script type="text/javascript">
        $(document).ready(function () {
            $('button').on('click', function () {

                // let text = this.value;
                // const myArray = text.split(","); 
                // var subject_id = myArray[0];

                let text = this.value;
                const myArray = text.split(","); 
                var subject_id = myArray[1];

                var course_id	= $('#cls_acadcrs').val();
				var period_id   = $('#cls_acadprd').val();
				var year_id 	= $('#cls_acadyr').val();
				var level_id 	= $('#cls_acadlvl').val();

                $.ajax({
                    type: 'POST',
					url: "../../model/class/classliststudent-class.php",
                    data:
                    {
                    	subject_id: subject_id,
                        course_id: course_id,
						period_id : period_id,
						year_id : year_id,
						level_id : level_id
	                },
                    success: function (data)
                    {
                       	$("#table-academic-student").show();
						$("#table-academic-student").html(data);
                    }
                });
            });
        });
    </script>