<?php
    // Before session_start()
    // ini_set('session.cookie_httponly', 1); // Prevent JavaScript access
    // ini_set('session.cookie_secure', 1);    // Send cookie only over HTTPS
    // ('session.cookie_samesite', 'Strict'); // Optional: prevent CSRF

    session_start();
    include_once '../configuration/connection-config.php';

    if (!isset($_SESSION['FULL_NAME'])) {
        header('Location: ../login-model.php');
        exit();
    }

    $whitelist = [
        'dashboard' => 'dashboard/dashboard-model.php',

        // academics
        'attendance' => 'academic/attendance/attendance-model.php',
        'grades' => 'academic/subjectschedulegrades/subjectschedulegrades-model.php',
        'prospectus' => 'academic/prospectus/prospectus-model.php',
        'schedule' => 'academic/schedule/schedule-model.php',
        'class list' => 'academic/classlist/classlist-model.php',
        'exam permit' => 'academic/examinationpermit/examinationpermit-model.php',
        'grading scale' => 'academic/gradingscale/gradingscale-model.php',
        'grades submission' => 'academic/submittedgrades/submitted-grades-model.php',
        'encoding status' => 'academic/grade-history/grade-history-model.php',

        // fees
        'tuition fee' => 'fees/tuition/tuition-model.php',
        'payment methods' => 'fees/payment-method/payment-model.php',

        // enrollment
        'online enrollment' => 'enrollment/online-enrollment/online-enrollment-model.php',
        'enrollment history' => 'enrollment/history/enrollment-history-model.php',
        'admission process' => '',
        // 'master list' => 'enrollment/master-list/master-list-model.php',
        'master list' => 'enrollment/master-list/master-list-model.php',
        'offered subjects' => 'enrollment/offered-subjects/subject-offered-model.php',
        'student grades' => 'enrollment/student-grades/grade-print-model.php',

        // forms
        'survey' => 'forms/survey/survey-model.php',
        'survey results' => 'forms/surveyresults/survey-result-model.php',
        'tadi' => 'forms/tadi/index.php',
        'tadi - dean' => 'forms/tadi/dean/index.php',
        'tadi - hr' => 'forms/tadi/humanresource/index.php',

        // site administration
        'accounts' => 'site-administration/module/userlist/userlist-model.php',
        'libraries updater' => 'site-administration/module/libraries-updater/index.php',
        
        // my account
        'profile' => 'myaccount/profile/profile-model.php',
        'account settings' => 'myaccount/account-settings/account-settings-model.php',
    ];

    // --- Default page (dashboard) ---
    if (!isset($_SESSION['page'])) {
        $_SESSION['page'] = 'dashboard';
    }

    // --- Validate against whitelist ---
    if (!array_key_exists($_SESSION['page'], $whitelist)) {
        // reset to dashboard instead of killing session
        $_SESSION['page'] = 'dashboard';
    }

    // --- Current page file ---
    $currentPage = $whitelist[$_SESSION['page']];

?>

<!DOCTYPE html>
<html lang="en">

<?php include '../partials/html_header.php' ?>

<body>
    <div class="d-flex">
        <?php include '../partials/sidebar.php' ?>

        <!-- Main Content -->
        <div class="d-flex flex-column w-100" id="contentContainer">
            <?php include '../partials/header.php' ?>

            <div class="w-100 text-end my-2">
                <div class="text-center" id="moduleName">
                    <p class="m-0"><?php echo ucwords($_SESSION['page']); ?></p>
                </div>
            </div>
            
            <div class="flex-grow-1" id="divContent">
                <?php 
                    // var_dump($_SERVER); 
						// echo '<pre>';
						// print_r($_SESSION);
						// echo '</pre>';
                ?>
                
                <?php 
					if ($_SESSION['page'] === "online enrollment") {
						$code = isset($_SESSION['STUDENT']['ID']) ? $_SESSION['STUDENT']['ID'] : 0;
						echo "<script>window.location.href='https://schoolportal.fcpc.edu.ph/onlineregistration/online-campus/login.php?code=" . $code . "';</script>";
					} else {
                        include_once $currentPage; 
                    }
                ?>
            </div>

            <?php include '../partials/footer.php' ?>
        </div>
    </div>

    <?php include '../partials/scripts.php' ?>
</body>

</html>