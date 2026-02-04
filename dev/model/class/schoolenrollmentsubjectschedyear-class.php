<?php 
	session_start();
	include('../configuration/connection-config.php');
    if(isset($_POST['level_id']))
    {

    	$_SESSION['LEVEL_ID'] = $_POST['level_id'];

        $qry ="SELECT DISTINCT `acadyr`.`schlacadyr_name` `ACADYR_NAME`, `lvl`.`schlacadlvlsms_id` `LVL_ID`, `ass`.`schlacadyr_id` `ACADYR_ID`
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
				WHERE `stud`.`schlstudsms_id` = ".$_SESSION['USERID']."  AND `ass`.`schlenrollass_status` = 1 AND `lvl`.`schlacadlvlsms_id` = ".$_SESSION['LEVEL_ID']." AND `type`.`sysusertype_name` =  '".$_SESSION['USERTYPE']."';";

    	$rsreg = $dbConn->query($qry);
    	$fetchacadyear = $rsreg->fetch_ALL(MYSQLI_ASSOC);
		$rsreg->close();
    	$createDropDown   = "<label>ACADEMIC YEAR</label>";
		$createDropDown  .= "<select id='subj_acadyr' name='acadyr' class='form-control' style='text-align:center;'>";
		$createDropDown .= "	<option value='0'>-- SELECT ACADEMIC YEAR --</option>";
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
            $('#subj_acadyr').change(function(){

                var year_id = $(this).val();
                var level_id = $('#subj_acadlvl').val();

                $.ajax({
                    type: "POST",
                    url: "../../model/class/schoolenrollmentsubjectschedperiod-class.php",
                    data:{
                        level_id : level_id,
						year_id : year_id
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



