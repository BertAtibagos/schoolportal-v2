<style>
    #div-grading-scale {
        overflow: auto;
        height: 200px;
    }

    #div-create-gs, #div-offered-subject-list, #div-message {
        display: none;
    }
    
    #gstbl {
        vertical-align: top;
    }
</style>


<section id="grading-scale">

	<?php include_once 'gradingscale-modal.php';?>

	<div id='div-message' class="alert alert-info" role="alert"></div>
    <div class="row"> 
        <div class="col-md-4 border-end" id='dropdown-container'>
            <div class="mb-3" id="dropdown-academic-level">
                <select id='gs-acadlvl' name='gs-acadlvl' class='form-select form-select-sm'></select>
            </div>
            <div class="mb-3" id="dropdown-academic-year">
                <select id='gs-acadyr' name='gs-acadyr' class='form-select form-select-sm'></select>
            </div>
            <div class="mb-3" id="dropdown-academic-period">
                <select id='gs-acadprd' name='gs-acadprd' class='form-select form-select-sm'></select>
            </div>
            <div class="mb-3" id="dropdown-academic-course">
                <select id='gs-acadcrse' name='gs-acadcrse' class='form-select form-select-sm'></select>
            </div> 
        </div>

        <div class='col-md-8' id='div-grading-scale'>
            <table id='table-grading-scale' class='table table-hover table-bordered'>
                <thead class='table-primary'>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id='tbody-grading-scale'>
                    <tr>
                        <td colspan='99'><span class='text-danger text-center'> No Record Found </span></td>
                    </tr>	
                </tbody>
            </table>
        </div>
    </div>
	<hr>

	<div class='my-2'>
		<button type="button" name="btnCreateGS" id="btnCreateGS" class="btn btn-sm btn-primary">Create New Scale</button>
	</div>

	<div id="div-create-gs">
        <form>
            <div class="row mt-2">
                <div class="col-md-3 mb-2">
                    <input type="text" class="form-control form-control-sm form-control form-control-sm-sm" id='gscale_code' name="gscale_code" placeholder="Grading System Code">
                </div>
                <div class="col-md-3 mb-2">
                    <input type="text" class="form-control form-control-sm form-control form-control-sm-sm" id='gscale_name' name="gscale_name" placeholder="Name">
                </div>
                <div class="col-md-3 mb-2">
                    <input type="text" class="form-control form-control-sm form-control form-control-sm-sm" id='details' name="details" placeholder="Description">
                    <label class="text-primary form-text ms-2"> This field is optional. </label>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm form-control form-control-sm-sm" id='pass_score' name="pass_score" placeholder="Passing Grade %" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');" maxlength="6">
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-between">
                <button type="button" name="btnAddComp" id="btnAddComp" class="btn btn-sm btn-success">Add Component</button>
                <input type="button" name='btnSubmitGS' id='btnSubmitGS' class="btn btn-sm btn-primary" value='Submit'>
            </div>

            <div id="div-new-gs-container" class="my-4">
            </div>
        </form>
	</div>

	<div id='div-subject'>
		<div id='div-offered-subject-list' class="mt-3">
			<table id='tbl-header-subject-list' class='table table-responsive table-bordered caption-top'>
                <caption>Grading Scale Information </caption>
				<tr>
					<td class="fw-medium w-25">Code</td>
					<td id='td-subj'></td>
                </tr>
				<tr>
					<td class="fw-medium w-25">Name:</td>
					<td id='td-crse-sec-sched'></td>
                </tr>
				<tr>
					<td class="fw-medium w-25">Description:</td>
					<td id='td-crse-sec-sched1'></td>
                </tr>
				<tr hidden>
					<td class="fw-medium w-25">ID:</td>
					<td id='td-crse-sec-sched2'></td>
                </tr>
				<tr hidden>
					<td class="fw-medium w-25">Passing Score:</td>
					<td id='td-crse-sec-sched3'></td>
                </tr>
			</table>

			<table id='table-subject' class='table table-hover table-responsive table-bordered table-fluid caption-top'>
                <caption>List of Offered Subjects</caption>
				<thead class='table-primary'>
					<tr>
						<th class="text-center"> <input type='checkbox' id='subjidall'> </th>
						<th class="text-center">#</th>
						<th class="text-start">Code</th>
						<th class="text-start">Description</th>
						<th class="text-center">Unit</th>
						<th class="text-start">Section</th>
						<th class="text-center">Count</th>
						<th class="text-start">Schedule</th>
						<th class="text-start">Instructor</th>
					</tr>
				</thead>
				<tbody id='tbody-subject'>
					<tr>
					    <td colspan='99'><span class='text-danger text-center'> No Record Found </span></td>
					</tr>	
				</tbody>
			</table>
			
			<input type='button' class="btn btn-sm btn-primary" id='btnSubmitTag' name='btnSubmitTag' value='Submit TAG'>
		</div>
	</div>

	<!-- Modals -->
	<div class="modal fade" id="editModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="label-modal" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="label-modal">Grading Scale</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>

				<div class="modal-body">
					<table id='table-modal-edit-header' class="table table-borderless table-responsive">
						<tbody>
							<tr hidden>
								<td>
									<label for="gs-code">ID:</label>
								</td>
								<td>
									<input type="text" class='form-control form-control-sm edit' name="gs-id" id="gs-id" readonly>
								</td>
							</tr>
							<tr>
								<td class="w-25">
									<label for="gs-code">CODE:</label>
								</td>
								<td>
									<input type="text" class='form-control form-control-sm edit' name="gs-code" id="gs-code">
								</td>
							</tr>
							<tr>
								<td class="w-25">
									<label for="gs-name">NAME:</label>
								</td>
								<td>
									<input type="text" class='form-control form-control-sm edit' name="gs-name" id="gs-name">
								</td>
							</tr>
							<tr>
								<td class="w-25">
									<label for="gs-desc">DESCRIPTION:</label>
								</td>
								<td>
                                    <input type="text" class='form-control form-control-sm edit' name="gs-desc" id="gs-desc">
                                    <label class='text-primary form-text ms-2'> This field is optional. </label>
								</td>
							</tr>
							<tr>
								<td class="w-25">
									<label for="gs-pass-score">PASSING SCORE:</label>
								</td>
								<td>
									<input type="text" class='form-control form-control-sm edit' name="gs-pass-score" id="gs-pass-score">
								</td>
							</tr>
						</tbody>
					</table>
					<table id='table-gscale' class='table table-hover table-responsive table-borderless table-fluid'>
						<tbody id='tbody-gscale'>
							<tr>
                                <td colspan='99'><span class='text-danger text-center'> No Record Found </span></td>
							</tr>	
						</tbody>
					</table>
				</div>
	
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary bntClose" data-bs-dismiss="modal">Close</button>
					<input type="button" class="btn btn-primary btnSaveChanges" value="Save Changes">
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="viewModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="label-modal" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<p class="modal-title fs-4 fw-medium" id="label-modal" name='gsview-name'>Grading Scale</p><p class="modal-title ms-1 fs-4 fw-medium" name='gsview-pass-score'></p>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
	
				<div class="modal-body">
					<table id='table-gsview' class='table-hover table-responsive table-borderless table-fluid'>
						<tbody id='tbody-gsview'>
							<tr>
                                <td colspan='99'><span class='text-danger text-center'> No Record Found </span></td>
							</tr>	
						</tbody>
					</table>
				</div>
	
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary bntClose" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</section>

<script src="../../js/custom/gradingscale-script.js?d=<?php echo time(); ?>"></script>
<script src="../../js/custom/gradingscale-create-script.js?d=<?php echo time(); ?>"></script>