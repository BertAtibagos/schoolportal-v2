<div class="modal fade " id="master-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="myModalLabel">Manage Student Academic Grades</h5>
				<button type="button" class="btn-close close" data-dismiss="modal" aria-label="Close" aria-hidden="true"></button>
			</div>

			<div id='div-message' class="text-danger"></div>

			<div class="modal-body">
				<div class="container-fluid">
					<table id='table-offered-subject' class='table table-bordered table-hover table-responsive'>
						<thead class='table-primary'>
							<tr>
								<th>Name:</th>
								<td id='th-student-name'></td>
							</tr>
							<tr>
								<th>A.Y. & Period:</th>
								<td id='th-year-period'></td>
							</tr>
						</thead>
					</table>
					<div id='div-tbl-gscale' class="form-group input-group">
					</div>
					<p class="text-end mb-0">
							FINAL AVERAGE (100%): <b id='b-final-average' class="text-danger fw-medium text-decoration-underline">0.0%</b>
					</p>
					<p class="text-start mb-0">
						  FINAL AVERAGE STATUS: <p id='p-final-average-status' class="text-danger fw-medium text-decoration-underline">
												PASSED
												</p>
												<select id='cbo-final-average-status' name='cbo-final-average-status' class='form-select form-select-sm w-50'>
													<option value='1'>PASSED</option>
													<option value='2'>FAILED</option>
													<option value='3'>NO GRADE</option>
													<option value='4'>IN PROGRESS</option>
													<option value='5'>INCOMPLETE</option>
												</select>
					</p>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success save">Save</button>
				<button type="button" class="btn btn-outline-secondary cancel" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
<?php
	//echo "<script src='../../js/custom/modal-script.js'></script>";
?>
