<link rel="stylesheet" type="text/css" href="forms/tadi/humanresource/css/hr.css">
<section>

    <div class="card-filter mb-3 p-3">
        <div class="row g-3 align-items-end flex-wrap">

            <div class="col-auto">
                <select class="form-select shadow" id="filterMode">
                    <option value="detailed">Detailed</option>
                    <option value="summary">Summary</option>
                </select>
            </div>

            <div class="col-auto">
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
        <div class="row">
            <div class="card col-md m-3 shadow stats verified">
                <h6>Total Verified</h6>
                <h3 id="verified">0</h3>
            </div>
            <div class="card col-md m-3 shadow stats unverified">
                <h6>Total Unverified</h6>
                <h3 id="unverified">0</h3>
            </div>
            <div class="card col-md m-3 shadow text-dark stats total-rec">
                <h6>Total Records</h6>
                <h3 id="total">0</h3>
            </div>
        </div>

        <div class="row">
            <div class="card col border shadow p-3 m-1 chart-container2">
                <canvas id="monthlyTotalChart"></canvas>
            </div>
            <div class="card col border shadow p-3 m-1 chart-container2">
                <canvas id="vertPerDeptChart"></canvas>
            </div>
        </div>

        <div class="row">
            <!-- <div class="card col border shadow p-3 m-1 chart-container1">
                <canvas id="totalChart"></canvas>
            </div> -->
            <div class="card col border shadow p-3 m-1 chart-container2">
                <canvas id="perCutOffChart"></canvas>
            </div>
        </div>
    </div>

</section>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
<script src="forms/tadi/humanresource/view/index-component.js?t=<?php echo time(); ?>" defer></script>
<script src="forms/tadi/humanresource/view/index-script.js?t=<?php echo time(); ?>" defer></script>
<script src="forms/tadi/humanresource/view/index-function.js?t=<?php echo time(); ?>" defer></script>
