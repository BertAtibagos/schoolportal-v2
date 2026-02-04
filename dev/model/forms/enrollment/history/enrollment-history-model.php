<!-- Filters -->
<div class="row mb-4 g-2">
    <div class="col-md-3">
        <select class="form-select">
            <option selected>Level</option>
            <option value="1">Tertiary</option>
            <option value="2">Senior High</option>
            <option value="3">Junior High</option>
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-select">
            <option selected>Year</option>
            <option>2024-2025</option>
            <option>2023-2024</option>
            <option>2022-2023</option>
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-select">
            <option selected>Period</option>
            <option>1st Sem</option>
            <option>2nd Sem</option>
            <option>Summer</option>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-select">
            <option selected>Course</option>
            <option>BSCS</option>
            <option>BSIT</option>
            <option>BSIS</option>
        </select>
    </div>
    <div class="col-md-1">
        <button class="btn btn-primary w-100">Search</button>
    </div>
</div>

<!-- Student Info -->
<div class="card shadow-sm mb-2">
    <div class="card-body">
        <h5 class="card-title">Student Information</h5>
        <p class="card-text">
            <strong>Name:</strong> Juan Dela Cruz<br>
            <strong>Student ID:</strong> 2021-12345<br>
            <strong>Program:</strong> BS Computer Science
        </p>
    </div>
</div>

<!-- Enrollment History Table -->
<div class="table-responsive">
    <div class="my-2 w-100 text-end">
        <button class="btn btn-outline-success"> Print </button>
        <button class="btn btn-outline-danger"> Export as PDF </button>
    </div>
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-primary text-center">
            <tr>
                <th>School Year</th>
                <th>Semester</th>
                <th>Subject Code</th>
                <th>Subject Description</th>
                <th>Units</th>
                <th>Section</th>
                <th>Status</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>2024-2025</td>
                <td>2nd Sem</td>
                <td>CS 420</td>
                <td>Software Engineering</td>
                <td>4</td>
                <td>BSCS 4B</td>
                <td>Enrolled</td>
                <td>—</td>
            </tr>
            <tr>
                <td>2024-2025</td>
                <td>1st Sem</td>
                <td>CS 405</td>
                <td>Artificial Intelligence</td>
                <td>3</td>
                <td>BSCS 4A</td>
                <td>Completed</td>
                <td>1.75</td>
            </tr>
            <tr>
                <td>2023-2024</td>
                <td>2nd Sem</td>
                <td>CS 301</td>
                <td>Database Management Systems</td>
                <td>3</td>
                <td>BSIT 3C</td>
                <td>Completed</td>
                <td>2.00</td>
            </tr>
            <tr>
                <td>2023-2024</td>
                <td>1st Sem</td>
                <td>CS 201</td>
                <td>Data Structures</td>
                <td>4</td>
                <td>BSCS 2B</td>
                <td>Completed</td>
                <td>1.50</td>
            </tr>
            <tr>
                <td>2022-2023</td>
                <td>2nd Sem</td>
                <td>SIPP 101</td>
                <td>Social Issues and Professional Practice</td>
                <td>3</td>
                <td>BSCS 3A</td>
                <td>Completed</td>
                <td>2.25</td>
            </tr>
        </tbody>
    </table>
</div>