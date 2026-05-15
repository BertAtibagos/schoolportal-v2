<link rel="stylesheet" href="forms/tadi/humanresource/css/hr.css?t=<?php echo time(); ?>">

<section class="hr-module">

    <div class="d-flex align-items-center gap-2 mb-3 ms-1">
        <div>
            <h5 class="mb-0 fw-bold" style="color: #032a74; letter-spacing: 0.3px;">TADI Records</h5>
            <small class="text-muted" style="font-size: 0.78rem;">Teaching &amp; Learning Activity Dashboard &mdash; Report Generator</small>
        </div>
    </div>

    <div class="card-filter mb-3 p-3">
        <div class="d-flex flex-wrap gap-2 align-items-end">

            <div class="col-auto">
                <select class="form-select shadow" id="filterMode">
                    <option value="detailed">Detailed</option>
                    <option value="summary">Summary</option>
                    <option value="tabulation">Tabulation</option>
                </select>
            </div>

             <div class="col-auto acad-lvl hide">
                <select class="form-select shadow" id="academiclevel" name="academiclevel">
                </select>
            </div>

            <div class="col-auto acad-lvl hide">
                <select class="form-select shadow" id="period" name="period">
                    <option value="" disabled selected>Period</option>
                </select>
            </div>

            <div class="col-auto acad-lvl hide">
                <select class="form-select shadow" id="acadyear" name="acadyear">
                    <option value="" disabled selected>School Year</option>
                </select>
            </div>

            <div class="col-auto cutoff-search">
                <select class="form-select shadow" id="perCutoffByDate">
                    <option value="currCutOff">Current cut off</option>
                    <option value="prevCutOff">Previous cut off</option>
                    <option value="date">By date</option>
                </select>
            </div>

            <div class="col-auto date-search hide">
                <input type="date" class="form-control shadow" id="startDate">
            </div>

            <div class="col-auto date-search hide">
                <input type="date" class="form-control shadow" id="endDate">
            </div>

            <div class="col-auto">
                <select class="form-select shadow" id="byAllNameDept">
                    <option value="all">All</option>
                    <option value="byName">By Name</option>
                    <option value="byDept">By Department</option>
                </select>
            </div>

            <div class="col-auto name-search hide">
                <input type="text" class="form-control shadow" placeholder="Name" id="nameSearch">
            </div>

            <div class="col-auto dept-select hide">
                <select class="form-select shadow" id="deptSelect">
                    <option value="COAM">College of Allied Medicine</option>
                    <option value="COLA">College of Liberal Arts</option>
                    <option value="COCS">College of Computer Studies</option>
                    <option value="COCJ">College of Criminal Justice</option>
                    <option value="COE">College of Engineering</option>
                    <option value="COA">College of Accountancy</option>
                    <option value="COBM">College of Business Management</option>
                    <option value="COED">College of Education</option>
                </select>
            </div>

            <div class="col-auto">
                <button id="generateBtn" class="btn px-4 shadow btn-secondary text-white gen-rep" disabled>
                    Generate Report
                </button>   
            </div>
        </div>
    </div>

    <div class="card mx-auto p-3 report-view" id="reportView">
        <div class="row g-2 mb-3">
            <div class="col-md-4">
                <div class="card shadow stats verified h-100">
                    <h6>Total Verified</h6>
                    <h3 id="verified">0</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow stats unverified h-100">
                    <h6>Total Unverified</h6>
                    <h3 id="unverified">0</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow stats total-rec h-100">
                    <h6>Total Records</h6>
                    <h3 id="total">0</h3>
                </div>
            </div>
        </div>

        <div class="row g-2">
            <div class="col-md card border shadow p-3 m-1 chart-container2">
                <canvas id="monthlyTotalChart"></canvas>
            </div>
            <div class="col-md card border shadow p-3 m-1 chart-container2">
                <canvas id="vertPerDeptChart"></canvas>
            </div>
        </div>

        <div class="row g-2 mt-0">
            <div class="col-md card border shadow p-3 m-1 chart-container2">
                <canvas id="perCutOffChart"></canvas>
            </div>
        </div>
    </div>

</section>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
<script src="forms/tadi/humanresource/view/index-component.js?t=<?php echo time(); ?>" defer></script>
<script src="forms/tadi/humanresource/view/index-script.js?t=<?php echo time(); ?>" defer></script>
<script src="forms/tadi/humanresource/view/index-function.js?t=<?php echo time(); ?>" defer></script>
