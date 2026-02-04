

<?php

	include 'classlist-query.php';

	$createDropDown  = "<table id='regtable' class='table table-hover table-responsive table-bordered' >";

	$createDropDown  .= "<tbody>";

	$createDropDown  .= "<tr>";
	$createDropDown  .= "	<td style='text-align:left;'><label type='label'> <b> ACADEMIC YEAR / PERIOD:</b> </label></td>";
	$createDropDown  .= "	<td style='text-align:left;'><label type='label'>".  $regitem['ACAD_YR/PRD'] ."</label></td>";
	$createDropDown  .= "</tr>";

	$createDropDown  .= "<tr>";
	$createDropDown  .= "	<td style='text-align:left;'><label type='label'> <b> SUBJECT CODE: </b> </label></td>";
	$createDropDown  .= "	<td style='text-align:left;'><label type='label'>".  $regitem['SUBJ_CODE'] ."</label></td>";
	$createDropDown  .= "</tr>";

	$createDropDown  .= "<tr>";
	$createDropDown  .= "	<td style='text-align:left;'><label type='label'> <b> SUBJECT DESCRIPTION: </b> </label></td>";
	$createDropDown  .= "	<td style='text-align:left;'><label type='label'>".  $regitem['SUBJ_DESC'] ."</label></td>";
	$createDropDown  .= "</tr>";

	$createDropDown  .= "<tr>";
	$createDropDown  .= "	<td style='text-align:left;'><label type='label'> <b> SUBJECT UNIT: </b> </label></td>";
	$createDropDown  .= "	<td style='text-align:left;'><label type='label'>".  $regitem['SUBJ_UNIT'] ."</label></td>";
	$createDropDown  .= "</tr>";

	$createDropDown  .= "<tr>";
	$createDropDown  .= "	<td style='text-align:left;'><label type='label'> <b> SECTION: </b> </label></td>";
	$createDropDown  .= "	<td style='text-align:left;'><label type='label'>".  $regitem['SEC'] ."</label></td>";
	$createDropDown  .= "</tr>";

	$createDropDown  .= "<tr>";
	$createDropDown  .= "	<td style='text-align:left;'><label type='label'> <b> INSTRUCTOR/PROFESSOR: </b> </label></td>";
	$createDropDown  .= "	<td style='text-align:left;'><label type='label'>".  $regitem['PROF'] ."</label></td>";
	$createDropDown  .= "</tr>";

	$createDropDown  .= "<tr>";
	$createDropDown  .= "	<td style='text-align:left;'><label type='label'> <b> SUBJECT UNIT: </b> </label></td>";
	$createDropDown  .= "	<td style='text-align:left;'><label type='label'>".  $regitem['SUBJ_UNIT'] ."</label></td>";
	$createDropDown  .= "</tr>";

	$createDropDown  .= "<tr>";
	$createDropDown  .= "	<td style='text-align:left;'><label type='label'> <b> SCHEDULE: </b> </label></td>";
	$createDropDown  .= "	<td style='text-align:left;'><label type='label'>".  $regitem['SUBJ_SCHED'] ."</label></td>";
	$createDropDown  .= "</tr>";	


	$createDropDown .= "</tbody>";
	$createDropDown .= "</table>";

	echo $createDropDown;
?> 