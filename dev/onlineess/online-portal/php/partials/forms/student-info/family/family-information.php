	

<section id="family-information-section" class="family-information-section">
	<div class="row mb-3">
		<div class="col-md-6" hidden>
			<label for="required_family" class="form-label">Required Family Member <span class="text-danger">*</span></label>
			<select id="required_family" name="required_family" class="form-control" required>
				<option value='father'>Father</option>;
				<option value='mother'>Mother</option>;
				<option value='guardian'>Guardian</option>;
			</select>
		</div>
		<div class="col-md-6" hidden>
			<p style='font-size: 18; font-style: italic; font-weight: bold;' class="mt-3">
				<span class="text-danger">*</span> Note : Information of 1 chosen relative is required, others are voluntary
			</p>
		</div>

			
		<?php include 'father-form.php';?>	   
		<?php include 'mother-form.php';?>	
		<br>
		<div class="row-mb-3">
			<div align="center" class="headline">
				<h2> &nbsp; PARENT STATUS</h2>
			</div>
			<div class="col-md-6"><br>			
				<label for="view_parent_status" class="form-label">Parent Status <span class="text-danger">*</span></label>
				<input readonly type="text" id="view_parent_status" name="view_parent_status" class="form-control-plaintext viewing">

			</div>
		</div>

		<?php include 'guardian-form.php';?>
			
	</div>
	
</section>
