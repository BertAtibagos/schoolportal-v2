<section id="class-list">
    <div id='div-message'>
    </div>
    <div id="div-header">
        <div class="row mb-2 align-items-end">
            <div id="dropdown-academic-level" class="col-md-2 mb-2">
                <select id='cbo-acadlvl' name='cbo-acadlvl' class='form-select form-select-sm'></select>
            </div>
            <div id="dropdown-academic-year" class="col-md-2 mb-2">
                <select id='cbo-acadyr' name='cbo-acadyr' class='form-select form-select-sm'></select>
            </div>
            <div id="dropdown-academic-period" class="col-md-2 mb-2">
                <select id='cbo-acadprd' name='cbo-acadprd' class='form-select form-select-sm'></select>
            </div>
            <div id="dropdown-academic-course" class="col-md-3 mb-2">
                <select id='cbo-acadcrse' name='cbo-acadcrse' class='form-select form-select-sm'></select>
            </div>
            <div class='col-md-2 mb-2'>
                <button id='btnsearch' name='btnsearch' class='btn btn-sm btn-primary btnsearch'> Search </button>
            </div>
        </div>
        <div class='my-2' id='div-offered-subject'>
            <!-- <div id='div-progressbar' class="progress-form-color">
				<div class="progress-label">Loading...
				<div id="progressbar"></div>
				</div> -->
        </div>
        <div>
            <table id='table-offered-subject' class='table table-hover table-responsive table-bordered'>
                <thead class='table-primary'>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th class="text-center">Unit</th>
                        <th>Course</th>
                        <th>Section</th>
                        <th>Schedule</th>
                        <th>Grading Scale</th>
                        <th class="text-center">No. of Students</th>
                        <th>Status</th>
                        <th>Action</th>
                        <th>Process</th>
                    </tr>
                </thead>
                <tbody id='tbody-offered-subject'>
                    <tr>
                        <td colspan='99' class="text-danger text-center"> No Record Found </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div id='div-student'>
        <button id='btnBack' type='button' id='btnBack' name='btnBack' class='btn btn-sm btn-primary mb-2'><i class="fa-solid fa-angle-left"></i> BACK </button>
        <div id='div-enrolled-student-list'>
            <p id='p-title' class="text-center d-none">
                List of Enrolled Student
            </p>
            <table id='tbl-header-student-list' class='table table-bordered table-responsive'>
                <tr>
                    <td class='w-25 fw-medium'>Code & Description</td>
                    <td id='td-subj'></td>
                </tr>
                <tr>
                    <td class='w-25 fw-medium' >Section / Schedule</td>
                    <td id='td-crse-sec-sched'></td>
                </tr>
            </table>
            <table id='table-student' class='table table-hover table-responsive table-bordered'>
                <thead class='table-primary'>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Student Name</th>
                        <th>Gender</th>
                        <th>Section</th>
                        <th>Status</th>
                        <th class="text-center">Final Average</th>
                        <th class="text-center">Average Status</th>
                        <th class="text-center">Action</th>
                        <th class='thallgrades' hidden>
                            <button type='button' id='btn-all-grades' name='btn-all-grades' class='btn btn-sm btn-primary mnuallgrades' hidden> All Grades </button>
                        </th>
                    </tr>
                </thead>
                <tbody id='tbody-student'>
                    <tr>
                        <td colspan='99' class="text-center text-danger"> No Record Found </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script src="../../js/custom/classlist-script.js?d=<?php echo time(); ?>"></script>
    <?php include_once 'modal-model.php'; ?>
</section>