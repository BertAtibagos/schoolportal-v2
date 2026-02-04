<?php  
	
	require_once('../configuration/connection-config.php');
	session_start();

	if( isset($_POST['course_id']) && isset($_POST['period_id']) && isset($_POST['year_id']) && isset($_POST['level_id']))
    {

		$qry = "SELECT 	`SCHL_ASS`.`schlacadlvl_id` `ACADLVL_ID`,
						`ACAD_LVL`.`schlacadlvl_NAME` `ACADLVL_NAME`,
					    `SCHL_ASS`.`schlacadyr_id` `ACADYR_ID`,
						`ACAD_YR`.`schlacadyr_name` `ACADYR_NAME`,
						`SCHL_ASS`.`schlacadprd_id` `ACADPRD_ID`,
						`ACAD_PRD`.`schlacadprd_name` `ACADPRD_NAME`,
						`SCHL_ASS`.`schlacadcrse_id` `ACADCRS_ID`,
						`ACAD_CRS`.`schlacadcrses_name` `ACADCRS_NAME`,
						
						`SCHL_INV`.`schlenrollinv_date` `INV_DATE`,
						`SCHL_INV`.`schlenrollinv_orno` `INV_ORNO`,
						`SCHL_INV`.`schlenrollinv_amt_tendered` `INV_AMT_TENDERED`
						
				FROM `schoolenrollmentassessment` `SCHL_ASS`
					LEFT JOIN `schoolenrollmentinvoice` `SCHL_INV`
						ON `SCHL_ASS`.`schlenrollasssms_id` = `SCHL_INV`.`schlenrollass_id`
						
					LEFT JOIN `schoolacademiclevel` `ACAD_LVL`
						ON `SCHL_INV`.`schlacadlvl_id` = `ACAD_LVL`.`schlacadlvlsms_id`
						
					LEFT JOIN `schoolacademicyear` `ACAD_YR`
						ON `SCHL_INV`.`schlacadyr_id` = `ACAD_YR`.`schlacadyrsms_id`
					
					LEFT JOIN `schoolacademicperiod` `ACAD_PRD`
						ON `SCHL_INV`.`schlacadprd_id` = `ACAD_PRD`.`schlacadprdsms_id`
						
					LEFT JOIN `schoolacademiccourses` `ACAD_CRS`
						ON `SCHL_ASS`.`schlacadcrse_id` = `ACAD_CRS`.`schlacadcrsesms_id`

				WHERE 	
						`SCHL_ASS`.`schlstud_id` =     " . $_SESSION['USERID'] . " AND
						`SCHL_INV`.`schlacadlvl_id` =  " . $_POST['level_id']  . " AND
						`SCHL_INV`.`schlacadyr_id` =   " . $_POST['year_id']   . " AND 
						`SCHL_INV`.`schlacadprd_id` =  " . $_POST['period_id'] . " AND 
						`SCHL_ASS`.`schlacadcrse_id` = " . $_POST['course_id'];

		$rsreg = $dbConn->query($qry);
	    $fetchDatareg = $rsreg->fetch_all(MYSQLI_ASSOC);

		$rsreg->close();

		$total_amount_paid = 0.00;
	    foreach ($fetchDatareg as $regitem)
	    {
			$total_amount_paid += $regitem['INV_AMT_TENDERED'];
		}
		

		$createTable  = "<table class='table'>";
	    $createTable .= "<tbody>";
	    $createTable .= "      <tr>";
	    $createTable .= "			<td style='text-align:LEFT;' width='350px'><label type='label' class='text-primary'><label type='label' class='text-danger'><b> TOTAL AMOUNT PAID: <u> ₱ " . $total_amount_paid . " </u> </b></label></td>";
	    $createTable .= "      </tr>";
	    $createTable .= "      <tr>";
	    $createTable .= "			<td style='text-align:LEFT;' width='350px'><label type='label' class='text-primary'><label type='label' class='text-danger'><b> TOTAL REMAINING BALANCE: <u> ₱ 8,650.00 </u> </b></label></td>";
		$createTable .= "			<td style='text-align:LEFT;'><label type='label' class='text-primary'></label></td>";
		$createTable .= "			<td style='text-align:center;'></td>";
		$createTable .= "			<td style='text-align:right;'><label type='label' class='text-primary'> " . $fetchDatareg[0]['ACADPRD_NAME'] . "(" . $fetchDatareg[0]['ACADYR_NAME'] . ") </label></td>";
	    $createTable .= "      </tr>";
	    $createTable .= "    </tbody>";
	    $createTable .= "</table>";

		$createTable .= "<table id='regtable' class='table table-hover table-responsive table-bordered'>";
		$createTable .= "	<thead class='table table-primary'>";
		$createTable .= "		<tr>";
		$createTable .= "			<th scope='col' style='text-align:center;'>#</th>";
		$createTable .= "			<th scope='col' style='text-align:center;'>Invoice Date</th>";
		$createTable .= "			<th scope='col' style='text-align:center;'>Invoice OR No.</th>";
		$createTable .= "			<th scope='col' style='text-align:center;'>Amount Paid</th>";
		$createTable .= "		</tr>";
		$createTable .= "	</thead>";
	    $createTable .= "	<tbody>";


	    $count = 1;
	    foreach ($fetchDatareg as $regitem)
	    {
			$createTable  .= "<tr>";
			$createTable  .= "	<td style='text-align:center;'><label type='label'>". $count++ ."</label></td>";
			$createTable  .= "	<td style='text-align:center;'><label type='label'>". $regitem['INV_DATE'] ."</label></td>";
			$createTable  .= "	<td style='text-align:center;'><label type='label'>". $regitem['INV_ORNO'] ."</label></td>";
			$createTable  .= "	<td style='text-align:center;'><label type='label'>". $regitem['INV_AMT_TENDERED'] ."</label></td>";
			$createTable  .= "</tr>";

	    }
	    $createTable .= "    </tbody>";
	    $createTable .= "</table>";




	    echo $createTable;




	}
?>