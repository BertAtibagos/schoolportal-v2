<style>
    .cards:hover {
        transform: scale(1.03);
        /* Slightly enlarge the card on hover */
        cursor: pointer;
    }

    .table-container {
        max-height: 40dvh !important;
        overflow: auto;
    }

    h5 {
        margin-bottom: 0;
    }

    a {
        text-decoration: none;
        color: unset
    }
</style>

<?php
    // var_dump($_SESSION);

    if (empty($_SESSION)) {
        session_destroy();
        header("Location: http://localhost/schoolportal");
    }

    if (isset($_SESSION['EMPLOYEE']) && isset($_SESSION['STUDENT'])) {
        // Both employee and student sessions exist
        $usertype = "Employee and Student";
    } elseif (isset($_SESSION['EMPLOYEE'])) {
        // Only employee session exists
        $usertype = "Employee";
    } elseif (isset($_SESSION['STUDENT'])) {
        // Only student session exists
        $usertype = "Student";
    } else {
        // Neither session exists
        $usertype = "NONE";
        session_destroy();
    }
?>


<!-- <div class="alert alert-info alert-dismissible fade show" role="alert"> -->
<div class="alert alert-info alert-dismissible fade show" role="alert" id="login-alert">
    <h4 class="alert-heading">Welcome back, <?= ucwords(strtolower($_SESSION['FIRST_NAME'])) ?>! 📣</h4>
    <hr>
    <p class="mb-0">You have successfully logged in as <strong><?= $usertype ?></strong> at <?= $_SESSION['LOGIN_TIME'] ?>.</p>
    <button type="button" class="btn btn-sm btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
</div>

<?php
    if (isset($_SESSION['EMPLOYEE'])) {
        if (str_contains($_SESSION['EMPLOYEE']['CATEGORY'], "INSTRUCTOR")) {
            require_once 'teacher-dashboard.php';
        }

        if (str_contains($_SESSION['EMPLOYEE']['CATEGORY'], 'DEAN')) {
            require_once 'dean-dashboard.php';
        }

        if (str_contains($_SESSION['EMPLOYEE']['CATEGORY'], 'ADMIN')) {
            require_once 'admin-dashboard.php';
        }
    }
		
    if (isset($_SESSION['STUDENT'])) {
        require_once 'student-dashboard.php';
    }

?>
<div class="row">
    <div class="col-md-5 mb-2 d-none">
        <div class="card">
            <div class="card-header ">
                <h5 class="card-title text-primary-emphasis my-1"><i class="fa-solid fa-circle-exclamation"></i> Announcements</h5>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Subject</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2026-01-08 08:00 AM</td>
                                <td>Enrollment Reminder</td>
                                <td>Enrollment for 2nd Semester opens Jan 15. Submit all requirements on time.</td>
                            </tr>
                            <tr>
                                <td>2026-01-08 08:00 AM</td>
                                <td>Enrollment Reminder</td>
                                <td>Updated syllabus for Fundamentals of Programming is now available in the portal.</td>
                            </tr>
                            <tr>
                                <td>2026-01-08 08:00 AM</td>
                                <td>Enrollment Reminder</td>
                                <td>School will be closed on Jan 12 for National Holiday.</td>
                            </tr>
                            <tr>
                                <td>2026-01-08 08:00 AM</td>
                                <td>Enrollment Reminder</td>
                                <td>Student course evaluations for Discrete Structures are now open. Deadline: Jan 10.</td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- <a href="" class="ps-2">See More...</a> -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title text-primary-emphasis my-1"><i class="fa-solid fa-box"></i> Activity Logs</h5>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <!-- <th style="width: .5rem;"></th> -->
                                <th>Date & Time</th>
                                <th>Activity</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php include_once "log-controller.php"; ?>
                        </tbody>
                    </table>
                </div>
                <!-- <a href="" class="ps-2">See More...</a> -->
            </div>
        </div>
    </div>
</div>

<!-- <script>
    document.addEventListener('DOMContentLoaded', () => {
        const alertEl = document.getElementById('login-alert');

        if (alertEl) {
            // Auto-dismiss after 5 seconds (5000ms)
            setTimeout(() => {
                const alert = new bootstrap.Alert(alertEl);
                alert.close();
            }, 2500);
        }
    });
</script> -->