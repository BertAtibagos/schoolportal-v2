<section id='sec-student-list'>
    <div class="row">
        <div class="col-md-2">
            <button id='btnBack' type='button' id='btnBack' name='btnBack' data-backdrop='static' data-keyboard='false' class='btn btn-sm btn-primary'>
                BACK
            </button>
        </div>
        <div class="col-md-8">

        </div>
        <div class="col-md-2 text-end">
            <button id='btnmodalprocessdenied' type='button' name='btnmodalprocessdenied' data-backdrop='static' data-keyboard='false' class='me-2 btn btn-sm btn-danger'>
                DENY
            </button>
            <button id='btnmodalprocessapproved' name='btnmodalprocessapproved' data-backdrop='static' data-keyboard='false' class='btn btn-success btn-sm'>
                APPROVE
            </button>
        </div>
    </div>
    <div id='div-student'>
        <div id='div-enrolled-student-list' class="mt-4">
            <table id='tbl-header-student-list' class='table table-bordered table-responsive'>
                <tr>
                    <td class='w-25 fw-medium'>Code & Description</td>
                    <td id='td-subj'></td>
                </tr>
                <tr>
                    <td class='w-25 fw-medium'>Section / Schedule</td>
                    <td id='td-crse-sec-sched'></td>
                </tr>
            </table>
            <table id='table-student' class='table table-hover table-responsive table-bordered caption-top'>
                <caption>List of Enrolled Students</caption>
                <thead class="table-primary">
                    <tr>
                        <th scope='col' class="text-center">#</th>
                        <th scope='col'>Name</th>
                        <th scope='col'>Gender</th>
                        <th scope='col'>Course & Section</th>
                        <th scope='col'>Status</th>
                        <th scope='col' class="text-center">Final Average</th>
                        <th scope='col' class="text-center">Average Status</th>
                        <th scope='col' class='thallgrades text-center'>Actions </th>
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
<div id="submitted-request">
    <div id='div-message'>
    </div>
    <div>
        <div class='row mb-4'>
            <div class='col-md-2 mb-2'>
                <div id="dropdown-academic-level">
                    <select id='cbo-acadlvl' name='cbo-acadlvl' class='form-select form-select-sm'></select>
                </div>
            </div>
            <div class='col-md-2 mb-2'>
                <div id="dropdown-academic-year">
                    <select id='cbo-acadyr' name='cbo-acadyr' class='form-select form-select-sm'></select>
                </div>
            </div>
            <div class='col-md-2 mb-2'>
                <div id="dropdown-academic-period">
                    <select id='cbo-acadprd' name='cbo-acadprd' class='form-select form-select-sm'></select>
                </div>
            </div>
            <div class='col-md'>
                <button id='btnsearch' name='btnsearch' class='btn btn-sm btn-primary btnsearch'> Search </button>
            </div>
        </div>
        <div class="row" id='div-student'>
            <div class="col" id='div-enrolled-student-list'>
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-for-approval-tab" data-bs-toggle="tab" data-bs-target="#nav-for-approval" type="button" role="tab" aria-controls="nav-for-approval" aria-selected="true">
                            <span id="spansubmittedgradesnotification" class='text-danger fw-medium me-s'></span>
                            For Approval
                        </button>
                        <button class="nav-link" id="nav-approved-tab" data-bs-toggle="tab" data-bs-target="#nav-approved" type="button" role="tab" aria-controls="nav-approved" aria-selected="false">Approved</button>
                        <button class="nav-link" id="nav-denied-tab" data-bs-toggle="tab" data-bs-target="#nav-denied" type="button" role="tab" aria-controls="nav-denied" aria-selected="false">Denied</button>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-for-approval" role="tabpanel" aria-labelledby="nav-for-approval-tab">
                        <div class='my-4'>
                            <table id='table-for-approval' class='table table-hover table-responsive table-bordered'>
                                <thead class='table-primary'>
                                    <tr>
                                        <th scope='col' class="text-center">#</th>
                                        <th scope='col'>Date & Time</th>
                                        <th scope='col'>Instructor</th>
                                        <th scope='col'>Subject</th>
                                        <th scope='col'>Course & Section</th>
                                        <th scope='col'>Status</th>
                                        <th scope='col'>Grading Scale</th>
                                        <th scope='col' class="text-center">Student</th>
                                        <th scope='col'>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id='tbody-for-approval'>
                                    <tr>
                                        <td colspan='99' class="text-center text-danger"> No Record Found </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-approved" role="tabpanel" aria-labelledby="nav-approved-tab">
                        <div class='my-4'>
                            <table id='table-approved' class='table table-hover table-responsive table-bordered'>
                                <thead class='table-primary'>
                                    <tr>
                                        <th scope='col' class="text-center">#</th>
                                        <th scope='col'>Date & Time</th>
                                        <th scope='col'>Instructor</th>
                                        <th scope='col'>Subject</th>
                                        <th scope='col'>Course & Section</th>
                                        <th scope='col'>Status</th>
                                        <th scope='col'>Grading Scale</th>
                                        <th scope='col'>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id='tbody-approved'>
                                    <tr>
                                        <td colspan='99' class="text-center text-danger"> No Record Found </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="table-wrapper" class="my-4"></div>
                    </div>
                    <div class="tab-pane fade" id="nav-denied" role="tabpanel" aria-labelledby="nav-denied-tab">
                        <div class='my-4'>
                            <table id='table-denied' class='table table-hover table-responsive table-bordered'>
                                <thead class='table-primary'>
                                    <tr>
                                        <th scope='col' class="text-center">#</th>
                                        <th scope='col'>Date & Time</th>
                                        <th scope='col'>Instructor</th>
                                        <th scope='col'>Subject</th>
                                        <th scope='col'>Course & Section</th>
                                        <th scope='col'>Status</th>
                                        <th scope='col'>Grading Scale</th>
                                        <th scope='col'>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id='tbody-denied'>
                                    <tr>
                                        <td colspan='99' class="text-center text-danger"> No Record Found </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="../../js/custom/submitted-grades-script.js?d=<?php echo time(); ?>"></script>
<?php
include 'submitted-grades-modal-model.php';
?>