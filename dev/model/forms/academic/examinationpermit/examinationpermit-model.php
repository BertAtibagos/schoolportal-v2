<link rel='stylesheet' href='../../css/custom/examinationpermit-style.css'/>

<style>
    .bd-modal-lg .modal-dialog {
        display: table;
        position: relative;
        margin: 0 auto;
        top: calc(50% - 24px);
    }

    .bd-modal-lg .modal-dialog .modal-content {
        background-color: transparent;
        border: none;
    }
	
	#div-student {
		display: none;
	}
</style>
<section id="class-list">
    <div id='div-message'>
    </div>
    <div id="div-header">
        <div class="row mb-2">
            <div id="dropdown-academic-level" class="col-md-2 mb-2">
                <select id='cbo-acadlvl' name='cbo-acadlvl' class='form-select form-select-sm'></select>
            </div>
            <div id="dropdown-academic-year" class="col-md-2 mb-2">
                <select id='cbo-acadyr' name='cbo-acadyr' class='form-select form-select-sm'></select>
            </div>
            <div id="dropdown-academic-period" class="col-md-2 mb-2">
                <select id='cbo-acadprd' name='cbo-acadprd' class='form-select form-select-sm'></select>
            </div>
            <div id="dropdown-academic-course" class="col-md-4">
                <select id='cbo-acadcrse' name='cbo-acadcrse' class='form-select form-select-sm'></select>
            </div>
            <div class="col-md-2">
                <button id='btnsearch' name='btnsearch' class='btn btn-sm btn-primary btnsearch'> Search </button>
            </div>
        </div>

        <div id='div-offered-subject'></div>

        <div>
            <table id='table-offered-subject' class='table table-hover table-responsive table-bordered caption-top'>
                <caption> List of Subjects </caption>
                <thead class='table-primary'>
                    <tr>
                        <th scope='col' class="text-center">#</th>
                        <th scope='col'>Code</th>
                        <th scope='col'>Description</th>
                        <th scope='col' class="text-center">Unit</th>
                        <th scope='col'>Course</th>
                        <th scope='col'>Section</th>
                        <th scope='col'>Schedule</th>
                        <th scope='col'>Grading Scale</th>
                        <th scope='col' class="text-center">No. of Student</th>
                        <th scope='col'>Status</th>
                        <th scope='col'>Action</th>
                    </tr>
                </thead>
                <tbody id='tbody-offered-subject'>
                    <tr>
                        <td colspan='99' class="text-center text-danger"> No Record Found </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div id='div-student'>
        <button id='btnBack' type='button' id='btnBack' name='btnBack' data-backdrop='static' data-keyboard='false' class='btn btn-sm btn-primary mb-2'> BACK </button>
        <div id='div-enrolled-student-list'>
            <table id='tbl-header-student-list' class="table table-bordered">
                <tr>
                    <td class="w-25 fw-medium">Subject</td>
                    <td id='td-subj'></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="w-25 fw-medium">Course & Section</td>
                    <td id='td-crse-sec-sched'></td>
                    <td></td>
                </tr>
            </table>
            <table id='table-student' class='table table-hover table-responsive table-bordered'>
                <thead class='table-primary' id='thead-student'>
                    <tr>
                        <th scope='col' class="text-center">#</th> 
                        <th scope='col'>Name</th> 
                        <th scope='col'>Gender</th> 
                        <th scope='col'>Course & Section</th> 
                        <th scope='col'>Status</th>
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
</section>

<script src='../../js/custom/examinationpermit-script.js?d=<?= time() ?>'></script>

<?php
    include_once 'examinationpermit-modal-model.php';
?>