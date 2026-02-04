<div class="container py-4">
    <div class="card p-3 mb-4 shadow-sm">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Category</label>
                <select class="form-select form-select-sm">
                    <option>Student</option>
                    <option>Employee</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Start Date</label>
                <input type="date" id="startDate" class="form-control form-control-sm">
            </div>

            <div class="col-md-4">
                <label class="form-label">End Date</label>
                <input type="date" id="endDate" class="form-control form-control-sm">
            </div>

            <div class="d-flex align-items-end">
                <button id="filterBtn" class="btn btn-primary btn-sm w-100">
                    Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="card p-3 shadow-sm">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="attendanceTable">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Date In</th>
                        <th>Time In</th>
                        <th>Date Out</th>
                        <th>Time Out</th>
                        <th>Source</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Juan Dela Cruz</td>
                        <td>Janaury 10, 2026</td>
                        <td>9:00</td>
                        <td>Janaury 10, 2026</td>
                        <td>16:00</td>
                        <td>HR Manual Input</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Maria Clara Batumbakal</td>
                        <td>Janaury 10, 2026</td>
                        <td>8:00</td>
                        <td>Janaury 10, 2026</td>
                        <td>17:00</td>
                        <td>Facial Recognition</td>
                        <td></td>
                    </tr>
                    <!-- Rows will be inserted here using JS or backend -->
                </tbody>
            </table>
        </div>
    </div>
</div>