<style>
	body, html {
		overflow: auto;
	}

	/* #myChart {
		width: 100%;
	}

	@media (max-width: 768px) {
		#myChart {
			height: 300%;
		}
	}

	@media (min-width: 769px) {
		#myChart {
			height: 10%;
		}
	} */
	
</style>

<section id="class-list" class="container-fluid" style="width: 100%; height: auto;">
	<div id='div-message'> 
	</div>
	<div class='container-fluid' style='width: auto; height: auto;margin: 10px 0 0 0;padding:0;'>
	
		<div class='row'>
				<div class='col-md-2 mb-2'>
					<div id="dropdown-academic-level">
						<label class='label'>LEVEL</label>
						<select id='stud-acadlvl' name='stud-acadlvl' class='form-select' style='text-align:left;font-size:12px;' required>
						<option value="academic_level">LEVEL</option>
						</select>
					</div>
				</div>
				<div class='col-md-2 mb-2'>
					<div id="dropdown-academic-year" style='display:none;'>
						<label class='label' id='stud-acadyr-lbl'>YEAR</label>
						<select id='stud-acadyr' name='stud-acadyr' class='form-select' style='text-align:left;font-size:12px;' required>
						<option value="2023-2024">YEAR</option>
						</select>
					</div>
				</div>
				<div class='col-md-2 mb-2'>
					<div id="dropdown-academic-period" style='display:none;'>
						<label class='label' id='stud-period-lbl'>PERIOD</label>
						<select id='stud-period' name='stud-period' class='form-select' style='text-align:left;font-size:12px;' required>
						<option value="academic_period">SEMESTER</option>
						</select>
					</div> 
				</div>
				<div class='col-md-2 mb-2'>
					<div id="dropdown-report-type" style='display:none;'>
						<label class='label' id='report-type-lbl'>REPORT TYPE</label>
						<select id='report-type' name='report-type' class='form-select' style='text-align:left;font-size:12px;' required>
						<option value="course_report">COURSE ENROLLEES BAR CHART</option>
						<option value="yr_lvl_report">YEAR LEVEL ENROLLEES PIE CHART</option>
						<option value="clsf_report">CLASSIFICATION OF ENROLLEES TABLE</option>
						<option value="all_report">SHOW ALL REPORT</option>
						</select>
					</div> 
				</div>
			<div class='col-md-2 mb-2' id='btnSearch-cont' style="display: none; align-items: end;">
				<button id='btnSearch' name='btnSearch'
						style='font-size: 12px;
								font-family: Roboto, sans-serif;
								font-weight: normal;width: auto;margin: 15 0 0 0;'
						class='btn btn-block btn-primary btnsearch'>
					Search
				</button>
			</div>
		</div>

		<!-- <style>

			#abc123 * {
				border:1px solid black;
				border-collapse: collapse;
			}

		</style>

		<table id="abc123">
  <thead>
    <tr>
		<th rowspan="2">subjs</th>
      <th colspan="2">head1</th>
      <th colspan="2">head2</th>
    </tr>
    <tr>
      <th>subhead1</th>
      <th>subhead1</th>
      <th>subhead2</th>
      <th>subhead2</th>
    </tr>
  </thead>
  <tbody>
    <tr>  
	<td>subj data</td> 
      <td>data1</td> 
      <td>data1</td>
      <td>data2</td>
      <td>data2</td>
    </tr>
    <tr>  

	<td>subj data</td> 
      <td>data1</td> 
      <td>data1</td>
      <td>data2</td>
      <td>data2</td>
    </tr>
    <tr>  
	<td>subj data</td> 
      <td>data1</td> 
      <td>data1</td>
      <td>data2</td>
      <td>data2</td>
    </tr> -->



		<table style="width: 100%; height: 100%;">
		<td>

  </tbody>
			<div id="enrollment_count_cont" class="container-fluid" style="font-size: 13px;
															font-family: Roboto, sans-serif;
															font-weight: bold;
															text-decoration: none;
															color: blue;
															text-align:left; width: 100%;height: 100%;
															display:none;
															">
										
										<style>
											#enrollment_count_tbl * {
												/* border: 0.5px solid rgb(0 40 88); */
												border-collapse: collapse;
												/* width: 100%; */
												margin: 15px 0 1px 0;
												padding: 1px; 
												font-size: 12px;
												font-family: Roboto, SANS-SERIF;
												font-weight: bold;
												text-decoration: none;
												color: black;
											}
											#enrollment_count_tbl thead * {
												border: 1px solid rgb(0 40 88);
											}
											#enrollment_count_tbl td:nth-child(even) {
												background-color: #cfe2ff;
											}
											#enrollment_count_tbl td:nth-child(odd) {
												background-color: #ffffff;
											} 
											/* #enrollment_count_tbl tr:nth-child(even) {
												background-color: #bacbe6;
											}
											#enrollment_count_tbl tr:nth-child(odd) {
												background-color: #ffffff;
											} */
											#enrollment_count_tbl th {
												background-color: rgba(10,50,150,0.5);
												/* color: rgba(0,255,0,1); neon green*/
												color: white;
											}

											/* #enrollment_count_tbl tr {
												background-color: white;
												color:white;
											} */

										</style>
										<table id='enrollment_count_tbl' class='table table-responsive table-striped table-hover' style='border-style:solid;'>
										<p id='title-label' class='title' style='width: 100%;margin:15px 0 1px 0; padding: 4px 0 4px 0;'>
											Enrollment Count Breakdown
										</p>
										<thead id='brkdwn_heads'>
											</thead>
											<canvas id="myChart" width="100%" height="30%"></canvas>
											<tbody id='brkdwn_bdy' 
												style='font-size: 1px;
													font-family: Roboto, sans-serif;
													font-weight: normal;
													text-decoration: none;
													color: black;'>
											</tbody>
										</table>
				
										
				</div>

				<div style='display: flex;'>
					<style>
						.rotating-text {
							margin: 0;
							animation: rotate 2s infinite linear;
						}

						@keyframes rotate {
							0% {
								transform: rotate(0deg);
							}
							100% {
								transform: rotate(360deg);
							}
						}
					</style>
					<div id="table-summary-cont" class="container-fluid" style="font-size: 13px;
					
												  font-family: Roboto, sans-serif;
												  font-weight: bold;
												  text-decoration: none;
												  color: blue;
												  text-align:right; width: 50%;height: 100%;
												  display:none">
						
						<table id='table-summary' style='width: 100%;margin:0; padding: 0; border-style:solid;' class='table table-responsive' >
						<p id='title-label' class='title' style='width: 100%;margin:15px 0 1px 0; padding: 4px 0 4px 0;'>
							Enrollment Summary
						</p>
						<tbody id='tbody-offered-subject' 
								   style='font-size: 10px;
									  font-family: Roboto, sans-serif;
									  font-weight: normal;
									  text-decoration: none;
									  color: black;'>
								<tr>
									<th style='width:auto;text-align:right;font-size: 11px;color: green;' class='table-primary'>NEW:</th>
									<td id='tdttlNEW' style='width:10%;font-size: 12px;color: blue;'>0</td>

									<th style='width:auto;text-align:right;font-size: 11px;color: green;' class='table-primary'>OLD:</th>
									<td id='tdttlOLD' style='width:10%;font-size: 12px;color: blue;'>0</td>
								</tr>
								<tr>
									<th style='width:auto;text-align:right;font-size: 11px;color: green;' class='table-primary'>TRANSFEREE:</th>
									<td id='tdttlTRANS' style='width:10%;font-size: 12px;color: blue;'>0</td>

									<th style='width:auto;text-align:right;font-size: 11px;color: green;' class='table-primary'>RETURNEE:</th>
									<td id='tdttlRETURN' style='width:10%;font-size: 12px;color: blue;'>0</td>
								</tr>
								<tr>
									<th style='width:auto;text-align:right;font-size: 11px;color: green;' class='table-primary'>TOTAL COURSE OFFERED</th>
									<td id = 'tdttlofferedcrse' style='font-size: 12px;color: blue;'>0</td>

									<th style='width:auto;text-align:right;font-size: 11px;' class='table-primary'>TOTAL ENROLLED STUDENT:</th>
									<td id = 'tdttlenrollstud' style='width:10%;font-size: 12px;color: blue;'>0</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div id="yr_lvl_tbl" class="container-fluid" style="font-size: 13px;
														font-family: Roboto, sans-serif;
														font-weight: bold;
														text-decoration: none;
														color: blue;
														text-align:left; width: 50%;height: 100%;
														display:none;">
									<p id='title-label' class='title' style='width: 100%;margin:15px 0 1px 0; padding: 4px 0 4px 0;'>
										Year Level Count
									</p>
									<table id='yrlvlcounttbl' style='width: 100%;margin:0; padding: 0; border-style:solid;' class='table table-responsive' >
										<tbody id='yrlvlcounttblbody' 
											style='font-size: 10px;
												font-family: Roboto, sans-serif;
												font-weight: normal;
												text-decoration: none;
												color: black;'>
											<tr>
												<!-- <th>loading...</th> -->
												<!-- <th id='yrlvlname' style='width:auto;text-align:right;font-size: 11px;color: green;' class='table-primary'>Year Levels</th>
												<td id='yrlvlcount' style='width:10%;text-decoration: underline;font-size: 12px;color: blue;'>Total</td> -->
												<td></td><td></td><td></td><td></td><td></td><td></td>
											</tr>
				
				
										</tbody>
									</table>
			
									
					</div>
				</div>


					
					
				</td>
				</table>


		 <div class="row"> 
			
		</div>
		
		
		
	</div>
</section>


<div style="display: none;" id="script_holder"></div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
    var currentDate = new Date();
    var dateString = currentDate.toISOString().replace(/[^0-9]/g, ''); // Remove non-numeric characters
    _string = '<script src="../../js/custom/enrollment-monitoring-script.js?d=' + dateString + '"></';
    _string2 = 'script>';
    $('#script_holder').html(_string + _string2);
</script>

<?php
	// echo "<link rel='stylesheet' href='../../css/custom/examinationpermit-style.css'/>";
	// include_once '../view/examinationpermit/examinationpermit-modal-model.php';
	include_once 'enrollment-monitoring-loading.php';
	include_once 'enrollment-monitoring-reminder-box.php';
?>
