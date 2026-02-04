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

	#loading_staff {
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

	

	<div class="container-fluid" id="staff-div-dashboard">

		<div class="card card-outline card-primary">
			<div class="card-body">
				<hr><br>
				<div align="center" class="headline">
					<h2> ENROLLMENT ( ADMISSION & ASSESSMENT SECTION ) </h2>
				</div><br><hr>

				<div class="row mb-3">
					<div class="col-md-2">
						<input type="text" id="staff-name-text" name="staff-name-text" placeholder="SEARCH ... "
							class="form-control " maxlength="50">
					</div>

					<div class="col-md-2" >
						<select  id="staff-name-select" name="staff-name-select" class="form-control " required>
							<option value="LAST_NAME"> LAST NAME </option>
							<option value="FIRST_NAME"> FIRST NAME </option>
							<option value="MIDDLE_NAME"> MIDDLE NAME </option>

						</select>
					</div>

					<div class="col-md-2 academiclevel" id="staff_academiclevel" >
						<select id="staff_academiclevelid" name="staff_academiclevelid" class="form-control staff_academiclevelid">
							<option value="1"> BASIC EDUCATION </option>
							<option value="2"> TERTIARY </option>
							<option value="3"> GRADUATE SCHOOL </option>
						</select>	
					</div>

					<div class="col-md-3 staff_admissionstatus" id="staff_admissionstatus" >
						<select id="staff_admissionstatusid" name="staff_admissionstatusid" class="form-control staff_admissionstatusid">
							<option value="0"> FOR ASSESSMENT </option>
							<option value="1"> ASSESSED </option>
						</select>	
					</div>

					<div class="col-md-3">
						<input type='button' class='btn btn-button-success' id="staff_search_button" name="staff_search_button" style='font-size: inherit;' value='SEARCH STUDENT/S'>
					</div>
				</div>

				<hr><br>
				<div align="center" class="headline">
					<h5> LIST OF REGISTERED STUDENTS </h5>
				</div><br><hr>

				<div id="loading_staff">
					<img src="../../image/Ripple-1s-287px.gif" alt="Loading ... ">
				</div>

				<table id='staff-table' class='paleBlueRows' style="font-size:12px;text-align: center; overflow: auto;"  width="100%">
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

					<tbody id='ess-staff-records'>
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

	

	
	<br><br><br><br><br>

	<?php
		//echo '<script src="../../js/EssStaff-script.js"></script>';
		//echo '<script src="../../js/EssStaff-functions.js"></script>';

	?>

</section>