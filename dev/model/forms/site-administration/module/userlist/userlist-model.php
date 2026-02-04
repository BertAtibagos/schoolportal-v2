<?php 
    // Check if session exists and validate user role
    if (!isset($_SESSION['EMPLOYEE']) && str_contains($_SESSION['CATEGORY'], 'ADMIN')) {
        http_response_code(403);
        die("You don't have access to this module.");
    }
?>
<section class="section-new-user">
    <div id="divSearch">
        <div class="row">
            <div class="col-md-3 mb-2">
                <select class="form-select" name="" id="usertype">
                    <option value="student"> Student </option>
                    <option value="instructor"> Instructor </option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mt-2">
                <input type="text" class="form-control" name="" id="inputtext">
            </div>
            <div class="col-md-3 mt-2">
                <select class="form-select" name="" id="inputtype">
                    <option value="lastname"> Last Name </option>
                    <option value="firstname"> First Name </option>
                </select>
            </div>
            <div class="col-md-2 mt-2">
                <button class="btn btn-primary" id="btnSearch">Search</button>
            </div>
        </div>

        <div class="mt-4">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button class="btn" id="adduser" style="background-color: #071976; color: #fff">Add new user</button>
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
        <div class="mt-4">
            <table class="table table-hover table-responsive-lg" id="userTable">
                <!-- <caption>List of users</caption> -->
                <thead class="table-primary bg-opacity-25" style="color: #071976;">
                    <th class="w-25 ps-4"> ID </th>
                    <th> Full name </th>
                    <th></th>
                </thead>
                <tbody>
                    <tr><td colspan="4" class="text-center text-danger" style="font-weight: 500">No matching record found</td></tr>
                </tbody>

            </table>
        </div>
    </div>
</section>
<script type="module" src="<?= assetLoader('../../js/custom/userlist-script.js') ?>"></script>