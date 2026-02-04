<?php include '../partials/loader.php' ?>
<section>
    <div id="errormessage"></div>
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
    </div>
    <div class="row">
        <div class='col-lg-6 my-2'>
            <select id='acadcourse' name='acadcourse' class='form-select form-select-sm'>
            </select>
        </div>
    </div>
    <div class="row">
        <div class='col-lg-2 my-2'>
            <select id='acadyearlevel' name='acadyearlevel' class='form-select form-select-sm'>
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
                <option value="last name">Last Name</option>
                <option value="first name">First Name</option>
            </select>
        </div>
        <div class='col-lg-2 my-2'>
            <button id='btnSearch' name='btnSearch' class='btn btn-primary btn-sm'> Search </button>
        </div>
    </div>

    <hr>
    <div id="divStudentTable">
        <!-- SEARCH RESULT TABLE -->
        <div class="mt-4">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <div class="dropdown">
                    <button class="btn btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><button class="dropdown-item rounded-0" value="topdf"><i class="pe-2 fa-solid fa-file-pdf"></i> PDF </button></li>
                        <li><button class="dropdown-item rounded-0" value="tocsv"><i class="pe-2 fa-solid fa-file-csv"></i> CSV </button></li>
                        <li><button class="dropdown-item rounded-0" value="toexcel"><i class="pe-2 fa-solid fa-table"></i> Excel </button></li>
                    </ul>
                </div>
            </div>
        </div>
        <div>
            <caption>Total: <span id="tableRowCount">0 enrolled students</span></caption>
        </div>
        <div id="divTableContainer">
            <table class="table table-hover table-bordered table-responsive-lg" id="userTable">
                <thead class="table-primary">
                    <th class="text-center">#</th>
                    <th style='width: 15%'>Student Number</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Gender</th>
                    <th>Year Level</th>
                    <th>Section</th>
                    <th>Status</th>
                    <!-- <th>Action</th> -->
                </thead>
                <tbody>
                    <tr><td colspan="99" class="text-center text-danger fw-medium">No matching records yet</td></tr>
                </tbody>

            </table>
        </div>

    </div>
</section>

<script type="module" src="<?= assetLoader('../../js/custom/master-list-script.js') ?>"></script>