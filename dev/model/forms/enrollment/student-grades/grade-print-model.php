<section>
    <style>
        .desc {
            width: 30dvw;
        }
    </style>
    <div id="errormessage" class="alert alert-danger" role="alert"></div>
    <div class="row">
        <div class='col-lg-2 my-2'>
            <select id='acadlevel' name='acadlevel' class='form-select form-select-sm'>
            </select>
        </div>
        <div class='col-lg-2 my-2'>
            <select id='acadyear' name='acadyear' class='form-select form-select-sm'>
            </select>
        </div>
        <div class='col-lg-2 my-2'>
            <select id='acadperiod' name='acadperiod' class='form-select form-select-sm'>
            </select>
        </div>
        <div class='col-lg-2 my-2'>
            <select id='acadyearlevel' name='acadyearlevel' class='form-select form-select-sm'>
            </select>
        </div>
    </div>
    <div class="row">
        <div class='col-lg-6 my-2'>
            <select id='acadcourse' name='acadcourse' class='form-select form-select-sm'>
            </select>
        </div>
        <div class='col-lg-2 my-2'>
            <select id='acadsection' name='acadsection' class='form-select form-select-sm'>
            </select>
        </div>
    </div>
    <div class="row">
        <div class='col-lg-4 my-2'>
            <input type="text" id='acadinfotext' name='acadinfotext' class='form-control form-control-sm'>
        </div>
        <div class='col-lg-2 my-2'>
            <select id='acadinfotype' name='acadinfotype' class='form-select form-select-sm'>
                <option value="lastname">Last Name</option>
                <option value="firstname">First Name</option>
            </select>
        </div>
        <div class='col-lg-2 my-2'>
            <button id='btnSearchName' name='btnSearchName' class='btn btn-primary btn-sm'> Search </button>
        </div>
    </div>

    <hr>
    <style>
        #divGradeTable {
            display: none;
        }
    </style>
    <div id="divStudentTable">
        <!-- SEARCH RESULT TABLE -->
        <div class="d-flex flex-row-reverse">
            <div class='my-2'>
                <button id='btnExportPdf' name='btnExportPdf' class='mx-1 btn btn-danger btn-sm' disabled> Export as PDF </button>
                <button id='btnExportExcel' name='btnExportExcel' class='mx-1 btn btn-success btn-sm d-none' disabled> Excel </button>
            </div>
        </div>
        <div id="divTableContainer">
            <table id="tblStudentList" class="table table-bordered table-hover">
                <thead class="table-primary">
                    <th class="text-center align-middle"><input type="checkbox" name="cbxCheckAll" id="cbxCheckAll"></th>
                    <th>ID Number</th>
                    <th>Student Name</th>
                    <th>Year Level</th>
                    <th>Course</th>
                    <th>Section</th>
                    <th class="text-center">Action</th>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </div>
    <div id="divGradeTable">
        <!-- SEARCH RESULT TABLE -->
        <div class="d-flex justify-content-between">
            <div class='my-2'>
                <button id='btnBack' name='btnBack' class='mx-1 btn btn-sm btn-primary'> Back </button>
            </div>
            <div class='my-2'>
                <button id='btnPrint' name='btnPrint' class='mx-1 btn btn-sm btn-success'> Print </button>
            </div>
        </div>
        <div id="divInfoTableContainer" class="my-4">
            <div class="row">
                <div class="col-md-2 text-start fw-medium">Student No.:</div>
                <div class="col text-start" id="studidno"></div>
            </div>
            <div class="row">
                <div class="col-md-2 text-start fw-medium">Student Name:</div>
                <div class="col text-start" id="studname"></div>
            </div>
            <div class="row">
                <div class="col-md-2 text-start fw-medium">Program:</div>
                <div class="col text-start" id="studcrse"></div>
            </div>
            <div class="row">
                <div class="col-md-2 text-start fw-medium">Year Level:</div>
                <div class="col text-start" id="studyrlvl"></div>
            </div>
            <!-- <div class="row">
                <div class="col-md-2 text-start fw-medium">Academic Year:</div>
                <div class="col text-start" id="studyrprd">2024 - 2025 (2nd Trimester)</div>
            </div> -->
        </div>
        <div id="divGradeContainer">
            <table id="tblStudentGrades" class="table table-bordered table-hover">
                <thead class="table-primary">
                    <th class="text-center">Course Code</th>
                    <th class="text-center">Description</th>
                    <th class="text-center">Units</th>
                    <th></th>
                    <th class="text-center">Final Grade</th>
                    <th class="text-center">Equivalent</th>
                    <th>Remarks</th>
                </thead>
                <tbody id="tbody-grade">
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-end fw-medium">Total units earned:</td>
                        <td class="text-center" id="total_unit"></td>
                        <td class="text-end fw-medium">GWA:</td>
                        <td class="text-center" id="final_grade"></td>
                        <td class="text-center" id="final_equivalent"></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <p class="mt-4 mb-1 fw-medium">Grading Equivalent </p>
        <div id="equiv-container">
        </div>
    </div>
</section>
<script src="../../js/custom/grade-print-script.js?d=<?php echo time(); ?>"></script>