<style type="text/css">

	.viewing{
		padding: 10px;
		border-bottom: 2px solid indianred;
		text-align: center;
	}

	/* input[type=text],input[type=date],input[type=number],input[type=email]{
		padding: 10px;
  		border-bottom: 2px solid indianred; 

	}*/

	/* select{
		padding: 12px 20px;
		margin: 8px 0;
		box-sizing: border-box;
		border: 3px solid #ccc;
	} */

	#loading_teamleader {
		display: none;
		position: fixed;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		background-color: rgba(255, 255, 255, 0.8);
		padding: 20px;
		border-radius: 10px;
		box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
		text-align: center;
	}
	
</style>

<section id="ess-staff-dashboard">

	<div class="container-fluid">
		<div class="card card-outline card-warning">
			<div class="card-body">
				<hr>
				<div align="center" class="header_div">
					<h2> ESS STAFF DASHBOARD </h2>
				</div><hr>
			</div>
		</div>
	</div>

	

	<div class="container-fluid" id="teamleader-div-dashboard">

		<div class="card card-outline card-primary">
			<div class="card-body">
				<hr><br>
				<div align="center" class="headline">
					<h2> ENROLLMENT ( ADMISSION & ASSESSMENT SECTION ) </h2>
				</div><br><hr>

				<div class="row mb-3">
					<div class="col-md-2">
						<input type="text" id="name-text" name="name-text" placeholder="SEARCH ... "
							class="form-control " maxlength="50">
					</div>

					<div class="col-md-2" >
						<select  id="name-select" name="name-select" class="form-control " required>
							<option value="LAST_NAME"> LAST NAME </option>
							<option value="FIRST_NAME"> FIRST NAME </option>
							<option value="MIDDLE_NAME"> MIDDLE NAME </option>

						</select>
					</div>

					<div class="col-md-2 academiclevel" id="academiclevel" >
						<select id="academiclevelid" name="academiclevelid" class="form-control academiclevelid">
							<option value="1"> BASIC EDUCATION </option>
							<option value="2"> TERTIARY </option>
							<option value="3"> GRADUATE SCHOOL </option>
						</select>	
					</div>

					<div class="col-md-3 admissionstatus" id="admissionstatus" >
						<select id="admissionstatusid" name="admissionstatusid" class="form-control admissionstatusid">
							<option value="0"> FOR ASSESSMENT </option>
							<option value="1"> ASSESSED </option>
						</select>	
					</div>

					<div class="col-md-3">
						<input type='button' class='btn btn-button-success' id="search_button" name="search_button" style='font-size: inherit;' value='SEARCH STUDENT/S'>
					</div>
				</div>

				<hr><br>
				<div align="center" class="headline">
					<h5> LIST OF REGISTERED STUDENTS </h5>
				</div><br><hr>

				<div id="loading_teamleader">
					<img src="../../image/Ripple-1s-287px.gif" alt="Loading ... ">
				</div>

				<table id='teamleader-table' class='paleBlueRows' style="font-size:12px;text-align: center; overflow: auto;"  width="100%">
					<thead>
						<tr>
							<th scope='col' >#</th>
							<th scope='col'>REGISTRATION DATE</th>
							<th scope='col'>STUDENT NAME</th>
							<th scope='col'>STUDENT TYPE</th>
							<th scope='col'>SOURCE</th>
							<th scope='col'>COURSE/STRAND</th>
							<th scope='col'>ESS STAFF ASSIGNED </th>
							<th scope='col'>STATUS</th>
							<th scope='col'>ACTIONS</th>
							<th scope='col'></th>

						</tr>
					</thead>

					<tbody id='ess-records'>
						<tr style="text-align: center;">
							<td colspan="10" style="text-align: center;">
								<button class='btn-design-danger'> NO RECORD FOUND </button> 
							</td>
						</tr>
					</tbody>
					
					<tfoot>
						<tr>
							<th scope='col' >#</th>
							<th scope='col'>REGISTRATION DATE</th>
							<th scope='col'>STUDENT NAME</th>
							<th scope='col'>SOURCE</th>
							<th scope='col'>STUDENT TYPE</th>
							<th scope='col'>COURSE/STRAND</th>
							<th scope='col'>ESS STAFF ASSIGNED </th>
							<th scope='col'>STATUS</th>
							<th scope='col'>ACTIONS</th>
							<th scope='col'></th>

						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>

	

	<div class="container-fluid" id="student-admission-info" hidden>
		<div class="card card-outline card-primary">
			<div class="card-body">

				<hr>
				<div align="center" class="headline">
					<h2>STUDENT INFORMATION</h2>
				</div><hr>

				<?php //include 'student-info/registration/form-registration.php'; ?>

				<?php include 'student-info/registration/educational-information.php'; ?>
				<?php include 'student-info/registration/student-information.php'; ?>
				<?php include 'student-info/registration/contact-information.php';?>
				<?php include 'student-info/registration/permanent-address.php';?>
				<?php include 'student-info/registration/present-address.php'; ?>
				<?php include 'student-info/registration/last-school-information.php'; ?>
				<?php include 'student-info/family/family-information.php'; ?>
				<?php include 'student-info/registration/knowledge-about-fcpc.php'; ?>
				<?php include 'student-info/registration/special-arrangements.php'; ?>
				<?php include 'student-info/registration/work-experience-information.php'; ?>


				<?php include 'student-info/registration/registration-requirements.php'; ?>

				<div class="esc_or_shs_div" hidden>
					<div align="center" style="background-color: indianred;">
						<h2>STUDENT ESC / VOUCHER INFORMATION</h2>
					</div>

					<div class="col-md-6" >
						<label for="esc_or_shs" class="form-label"> SELECT ESC / VOUCHER  <span class="text-danger">*</span></label>
						<select id="esc_or_shs" name="esc_or_shs" class="esc_or_shs form-control">
						</select>
					</div>
					<br><hr><br>
				</div>

				<hr><br>

				<div class="row mb-3" style="text-align: center;">
					<div class="col-md-4">
						<input type="button" id='btnAssess' class="btn btn-warning" style="font-size: 25px;" value=" VERIFY ADMISSION"/>        
					</div>

					<div class="col-md-4 cancel-btn-container" hidden>
						<input type="button" id='btnCancel' class="btn btn-outline-danger" style="font-size: 25px;" value="CANCEL ADMISSION"/>
					</div>

					<div class="col-md-4">
						<input type="button" id='viewDashboard' class="btn btn-info" style="font-size: 25px;" value="GO BACK TO DASHBOARD"/>
					</div>
				</div>

				<!-- <center>
					<div class="div_assess">
						<input type="button" id='btnAssess' class="btn btn-warning" style="font-size: 25px;" value="VERIFY STUDENT ASSESSMENT"/>
						<input type="button" id='btnCancel' class="btn btn-outline-danger" style="font-size: 25px;" value="CANCEL ADMISSION"/>
						<input type="button" id='viewDashboard' class="btn btn-info" style="font-size: 25px;" value="GO BACK TO DASHBOARD"/>
					</div>
				</center> -->
			</div>
		</div>
	</div>
	
	<br><br><br><br><br>

	<?php
		echo '<script src="../../js/EssTeamleader-functions.js"></script>';
		echo '<script src="../../js/EssTeamleader-script.js"></script>';
	?>

</section>