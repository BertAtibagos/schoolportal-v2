<link href= "../js/jquery/jquery.ui/ui/1.10.4/css/jquery-ui.css" rel="stylesheet" />
<link href= "../js/jquery/jquery.ui/ui/1.10.4/css/jquery-ui.min.css" rel="stylesheet" />
<link href= "../css/custom/progressbar-style.css" rel="stylesheet" />
<script src= "../js/jquery/jquery.ui/ui/1.10.2/js/jquery.min.js"></script>
<!-- <script src= "../js/jquery/jquery-1.9.1.js"></script>-->
<!-- <script src= "../js/jquery/jquery.ui/ui/1.10.4/js/jquery-ui.js"></script> -->
<script src= "../js/jquery/jquery.ui/ui/1.10.4/js/jquery-ui.min.js"></script>
<script src= "../../js/jquery/jquery.session-1.0.0/jquery.session.js"></script>
<script src= "../../js/jquery/jquery.datatables/1.10.10/jquery.datatables.min.js"></script>
<style>
	table tr th,thead tr th,tr th,th,table thead,thead {
		text-align:center;
		vertical-align: middle;
		margin:0;
		padding:0;
		text-decoration: underline;
		font-size: 11px;
		font-family: Roboto, sans-serif;
		font-weight: bold;
		text-decoration: none;
		color: blue;
	}
	
	table tr td {
		text-align:center; 
		vertical-align: middle;
		width: auto;
		height: auto;
		font-size: 10px;
		font-family: Roboto, sans-serif;
		font-weight: bold;
		text-decoration: none;
		color: black;
	}
	
	tbody {
		font-size: 10px;
		font-family: Roboto, sans-serif;
		font-weight: normal;
		text-decoration: none;
		color: black;
	}
	.title {
		text-decoration: underline;
		text-align: center;
		font: italic 18px cambria, serif; 
		width: 860px;
		padding: 5 0 5 0;
		margin: 0 0 1 0;
		color: darkblue;
		background-color: rgba(10,50,150,0.5);
	}
	
	#progressbar .ui-progressbar-value{
		background-color: rgba(20,20,255,0.5);
	}
	.progress-form-color{
		position: fixed;
		/*background-color: rgba(10,10,0,0.7);*/
		width: 56%;
		height: 30%;
		margin: 0;
		padding: 0;
		/*width: auto;*/
		/*height: 100%;*/
		background-repeat: no-repeat;
		background-position: center;
		background-size: 100%;
		background-color: transparent;
		background-image:linear-gradient(to bottom, 
		rgba(10,10,0,0.7) 10%,
		rgba(10,10,0,0.2) 50%);
		/*rgba(0,10,255,0.3) 10%,*/
		/*rgba(20,20,255,0.8) 50%);*/
	}
	.ui-progressbar {
		position: fixed;
		margin: 0;
		padding: 0;
		width: 30%;
		height: 3%;
		/*top: 44%;*/
		/*left: 20%;*/
		/*right: 20%;*/
		/*bottom: 44%;*/
	}
	.progress-label {
		position: relative;
		padding: 0;
		margin:0;
		height: auto;
		color: yellow;
		font-size: 19px;
		text-shadow: 2px 2px 0 black;
		font-family: Roboto, sans-serif;
		text-decoration: none;
		top: 25%;
		left: 22%;
		right: 22%;
		bottom: 30%;
	}
	.label {
		text-align:
		left;
		font-size:11px;
	}
	
	.viewstudentactive {
		font-size: 10px;
		font-family: Roboto, sans-serif;
		font-weight: normal;
	}
	
	.viewstudentinactive {
		font-family: Roboto, sans-serif;
		font-weight: normal;
		text-decoration: none;
		color: red;
	}
</style>
<section id="class-list" class="container-fluid" style="width: 100%; 
														height: 100%;">
	<div id='div-message'> 
	</div>
	<div class='container-fluid' style='margin: 0;padding:0;'>
		 <div class="row"> 
			<div class="col-md-4 vertical" style="border-right: 1px solid black;
												  font-size: 13px;
												  font-family: Roboto, sans-serif;
												  font-weight: bold;
												  text-decoration: none;
												  color: blue;
												  text-align:left; width: auto;">
				<div id="dropdown-academic-level">
					<label class='label'>LEVEL</label>
					<select id='cbo-acadlvl' name='cbo-acadlvl' class='form-control' style='text-align:left;font-size:11px;width: 320px;' required>
					</select>
				</div>
				<div id="dropdown-academic-year">
					<label class='label'>YEAR</label>
					<select id='cbo-acadyr' name='cbo-acadyr' class='form-control' style='text-align:left;font-size:11px;width: 320px;' required>
					</select>
				</div>
				<div id="dropdown-academic-period">
					<label class='label'>PERIOD</label>
					<select id='cbo-acadprd' name='cbo-acadprd' class='form-control' style='text-align:left;font-size:11px;width: 320px;' required>
					</select>
				</div>
				<div id="dropdown-academic-course">
					<label class='label'>COURSE</label>
					<select id='cbo-acadcrse' name='cbo-acadcrse' class='form-control' style='text-align:left;font-size:11px;width: 320px;' required>
					</select>
				</div> 
			</div>
			<div class='col-md-8' id='div-offered-subject' style='overflow: scroll;width: 840px;margin:0; padding: 0;'>
				<div id='div-progressbar' class="progress-form-color">
					<div class="progress-label">Loading...
					<div id="progressbar"></div>
					</div>
				</div>
				<div class='container-fluid' >
					<p class='title'>
						List of Subjects
					</p>
					<table id='table-offered-subject' style='width: auto;margin:0; padding: 0;' class='table table-hover table-responsive table-bordered'>
						<thead class='table-primary'
							   style='font-size: 11px;
									font-family: Roboto, sans-serif;
									font-weight: normal;
									text-decoration: none;
									color: blue;'>
							<tr>
								<th scope='col'>#</th>
								<th scope='col'>CODE</th>
								<th scope='col'>DESCRIPTION</th>
								<th scope='col'>UNIT</th>
								<th scope='col'>COURSE</th>
								<th scope='col'>SECTION</th>
								<th scope='col'>SCHEDULE</th>
								<th scope='col'>GRADING SCALE</th>
								<th scope='col'>NO. OF STUDENT</th>
								<th scope='col'>STATUS</th>
								<th scope='col'>VIEW</th>
								<th scope='col'>PROCESS</th>
							</tr>
						</thead>
						<tbody id='tbody-offered-subject' 
							   style='font-size: 10px;
								  font-family: Roboto, sans-serif;
								  font-weight: normal;
								  text-decoration: none;
								  color: black;'>
							<tr>
							<td colspan='12' style='font-size: 18px;
													font-family: Roboto, sans-serif;
													font-weight: normal;
													text-decoration: none;
													color: red;'>
							No Record Found
							</td>
							</tr>	
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<hr>
	<div class='container-fluid' id='div-student' 
		 style='margin: 0 0 20px 0;padding: 0 0 20px 0;'>
		<div id='div-enrolled-student-list' class="container-fluid">
			<p id='p-title' class='title' style='width: 100%; margin:0; padding: 0;'>
			List of Enrolled Student
			</p>
			<table id='tbl-header-student-list' class='table table-responsive' style='width: 100%; margin:0 0 1 0; padding:0;border-color: transparent;'>
				<tr>
					<td class='table-primary' style='color: blue;width: auto;text-align: left;'>SUBJECT</td>
					<td class='table-primary' style='width: 70%;text-align: left;' id='td-subj'></td>
					<td class='table-primary' style='width: 12%;'></td>
				</tr>
				<tr>
					<td class='table-primary' style='color: blue;width: auto;text-align: left;'>COURSE / SECTION / SCHEDULE</td>
					<td class='table-primary' style='width: 70%;text-align: left;' id='td-crse-sec-sched'></td>
					<td class='table-primary' style='width: 12%;'></td>
				</tr>
			</table>
			<table id='table-student' class='table table-hover table-responsive table-bordered' style='width: 100%; margin:0; padding:0;'>
				<thead class='table-primary'>
					<tr>
						<th scope='col' style='padding:0;margin:0;'>#</th>
						<th scope='col' style='padding:0;margin:0;'>NAME</th>
						<th scope='col' style='padding:0;margin:0;'>GENDER</th>
						<th scope='col' style='padding:0;margin:0;'>COURSE/SECTION</th>
						<th scope='col' style='padding:0;margin:0;'>STATUS</th>
						<th scope='col' style='padding:0;margin:0;'>FINAL AVERAGE</th>
						<th scope='col' style='padding:0;margin:0;'>AVERAGE STATUS</th>
						<th scope='col' style='padding:0;margin:0;' class='thallgrades'>
							<button type='button'
								style='font-size: 14px;
								font-family: tahoma, sans-serif;
								font-weight: normal;
								font-style: italic;
								text-decoration: underline;
								margin: 2 0 2 0;
								padding: 3 5 3 5;
								color: lime;'
								id='btn-all-grades' name='btn-all-grades' class='btn btn-block btn-primary mnuallgrades' value=''>
								All Grades
							</button>
						</th>
					</tr>
				</thead>
				<tbody id='tbody-student'>
					<tr>
						<td colspan='8' style='font-size: 18px;
												font-family: Roboto, sans-serif;
												font-weight: normal;
												text-decoration: none;
												color: red;'>
						No Record Found
						</td>
					</tr>	
				</tbody>
			</table>
			<?php
				
				echo "<script src='../../js/custom/classlist-script.js'></script>";
				echo "<script src='../../js/custom/progressbar-script.js'></script>";
				//include_once 'classlist-sub-model.php';
				include_once 'modal-model.php';
			?>
		</div>
	</div>
</section>
			