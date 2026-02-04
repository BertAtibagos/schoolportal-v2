
<section>

	<br>
	<div align="center" class="header_div">
		<h2> EDUCATIONAL INFORMATION FORM </h2>
	</div>
	

	<div class="row mb-3"><br>

		<div class="col-md-6"><br>
			<label for=student_type class="form-label">STUDENT TYPE <span class="text-danger">*</span></label>
			<input type="text" id="view_student_type" name="view_student_type" class="form-control-plaintext viewing" maxlength="50" readonly>            
		</div>

		<div class="col-md-4 view_lrn_number_div" hidden><br>
			<label for="view_lrn_number" class="form-label lrn_number">LRN NUMBER <span class="text-danger">*</span></label>
			<input type="text" id="view_lrn_number" name="view_lrn_number"
				class="form-control-plaintext viewing lrn_number" maxlength="50" readonly>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-md-6">
			<label for=view_academiclevelid class="form-label">ACADEMIC LEVEL <span class="text-danger">*</span></label>
			<input type="text" id="view_academiclevelid" name="view_academiclevelid" 
				class="form-control-plaintext viewing" maxlength="50" readonly>            
		</div>
		<div class="col-md-6">
			<label for="view_academicyearlevelid" class="form-label">YEAR LEVEL <span class="text-danger">*</span></label>
			<input type="text" id="view_academicyearlevelid" name="view_academicyearlevelid" 
				class="form-control-plaintext viewing" maxlength="50" readonly>            
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-md-6">
			<label for=view_academicperiodid class="form-label">PERIOD <span class="text-danger">*</span></label>
			<input type="text" id="view_academicperiodid" name="view_academicperiodid" 
				class="form-control-plaintext viewing" maxlength="50" readonly> 
		</div>

		<div class="col-md-6 form-group row">
			<label for="view_academicyearid" class="form-label">ACADEMIC YEAR <span class="text-danger">*</span></label>
			<input type="text" id="view_academicyearid" name="view_academicyearid" 
				class="form-control-plaintext viewing" maxlength="50" readonly>         
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-md-6">
			<label for="view_admission_type"class="form-label">ADMISSION TYPE <span class="text-danger">*</span></label>
			<input type="text" id="view_admission_type" name="view_admission_type" class="form-control-plaintext viewing" maxlength="50" readonly>            
		</div>
	</div>   

	<br><hr><br>

	<div class="row mb-3">
		<div class="col-md-12 view_academiccourse_div">
			<label for="view_academiccourseid" class="form-label">STRAND/PROGRAM/COURSE <span class="text-danger">*</span></label>
			<input type="text" id="view_academiccourseid" name="view_academiccourseid" class="form-control-plaintext viewing" maxlength="50" readonly>            
		</div>
	</div>

	<br>

	<div class="row mb-3 view_section_subjects" id="view_section_subjects" name="view_section_subjects">

		<label for="" class="form-label"> SECTION AND SUBJECT SCHEDULE <span class="text-danger">*</span></label>
		<div class="col-md-12" id="view_section_subjects_container" name="view_section_subjects_container"></div>

	</div> 
		

</section>