<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* -------------------------------
   SECURITY SETTINGS
-------------------------------- */
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Define the bypass code (can be stored securely elsewhere)
$maintenanceBypassCode = '011000';

// Check if maintenance mode is enabled
$_SESSION['maintenance'] = 0;

if (!empty($_SESSION['maintenance']) && $_SESSION['maintenance'] === 1) {

    // Allow bypass if correct code is in the URL
    if (isset($_GET['force']) && $_GET['force'] === $maintenanceBypassCode) {
        // Set session flag to allow bypass on future requests
        $_SESSION['bypass_maintenance'] = true;
    }

    // Redirect to maintenance page if not bypassed
    if (empty($_SESSION['bypass_maintenance'])) {
        session_destroy();
        header('Location: https://schoolportal.fcpc.edu.ph/maintenance.php');
        // header('Location: http://localhost/schoolportal/maintenance.php');
        exit("Site on maintenance");
    }
}

/* -------------------------------
   ALREADY LOGGED IN
-------------------------------- */
if (!empty($_SESSION['FULL_NAME'])) {
    header('Location: forms/masterpage-model.php', true, 302);
    exit;
}


/* -------------------------------
   GOOGLE OAUTH SETUP
-------------------------------- */
require '../components/vendor/autoload.php';
require 'configuration/connection-config.php';

$client = new Google\Client();
$client->setClientId(CLIENT_ID);
$client->setClientSecret(CLIENT_SECRET);
$client->setRedirectUri(CLIENT_REDIRECT);

/* 🔐 Least privilege scopes */
$client->addScope(['email', 'profile']);

/* 🔐 CSRF / STATE PROTECTION */
if (empty($_SESSION['oauth_state'])) {
    $_SESSION['oauth_state'] = bin2hex(random_bytes(16));
}
$client->setState($_SESSION['oauth_state']);

/* 🔐 Generate OAuth URL */
$url = $client->createAuthUrl();

/* 🔐 General CSRF token (forms, etc.) */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$stmt = $dbConn->prepare("SELECT 
        `lvl`.`SchlAcadLvl_NAME` `LVL_NAME`,
        `yr`.`SchlAcadYr_NAME` `YR_NAME`,
        `prd`.`SchlAcadPrd_NAME` `PRD_NAME`
        
        FROM `schoolacademicyearperiod` `yrprd`
        
        LEFT JOIN `schoolacademiclevel` `lvl`
        ON `yrprd`.`SchlAcadLvl_ID` = `lvl`.`SchlAcadLvlSms_ID`
        LEFT JOIN `schoolacademicyear` `yr`
        ON `yrprd`.`SchlAcadYr_ID` = `yr`.`SchlAcadYrSms_ID`
        LEFT JOIN `schoolacademicperiod` `prd`
        ON `yrprd`.`SchlAcadPrd_ID` = `prd`.`SchlAcadPrdSms_ID`
        
        WHERE `yrprd`.`SchlAcadYrPrd_ISOPEN` = 1");
$stmt->execute();
$result = $stmt->get_result(); // get the mysqli result

if ($result->num_rows > 0) {
    $year = [];
    $open_same = '';
    $open_diff = '';
    $title_text = '📣 Enrollment Ongoing:';
    $content_text = '';
    while ($row = $result->fetch_assoc()){ //fetch data
        $year[] = str_replace(' ', '', $row["YR_NAME"]);

        $formattedLevel = ucwords(strtolower($row['LVL_NAME']));
        $formattedPeriod = ucwords(strtolower($row['PRD_NAME']));
        
        $open_same .= "<p class='mb-0'>{$formattedLevel} [{$formattedPeriod}]</p>";
        $open_diff .= "<p class='mb-0'>{$formattedLevel} - {$formattedPeriod} [" . implode(' - ', explode('-', $row["YR_NAME"])) . "]</p>";
    }

    if (!empty($year) && count(array_unique($year)) === 1) { // if all year names are the same
        $title_text = "Enrollment Ongoing for " . implode(' - ', explode('-', $year[0])) . "! 📣";
        $content_text .= $open_same;
    } else {
        $content_text .= $open_diff;
    }
} else {
    $content_text .= 'No enrollment ongoing.';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCPC | School Portal</title>
    <link rel="icon" href="../images/fcpc_logo.ico" />
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/fontawesome/fontawesome.min.css">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../assets/bootstrap/bootstrap.min.css">
    <script src="../assets/bootstrap/bootstrap.bundle.min.js"></script>

    <!-- JQuery -->
    <script src="../assets/jquery/jquery.min.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/custom/login-style.css?<?= time() ?>">
</head>

<body>
	<!-- What's New Modal -->
	<div class="modal fade" id="whatsNewModal" tabindex="-1" aria-labelledby="whatsNewModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content">
		  <div class="modal-header bg-primary-subtle text-white">
			<h5 class="modal-title" id="whatsNewModalLabel"><i class="fa-solid fa-bolt"></i> What's New in the Portal!</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  </div>
		  <div class="modal-body">
			<p>Hello students! 🎉 </p>
			<p>We’ve just updated the school portal to make your experience smoother and more convenient. Here’s what’s new:</p>
			<ul class="list-group list-group-flush">
			  <li class="list-group-item"><strong>Login with Google:</strong> Sign in quickly and securely using your FCPC Google account.</li>
			  <li class="list-group-item"><strong>Tuition Fee Viewing:</strong> Check your tuition fees anytime directly on the portal.</li>
			  <li class="list-group-item"><strong>Schedule Viewing:</strong> View your class schedule easily, including any updates from the Registrar.</li>
			</ul>
			<p class="mt-3"><strong>Tip:</strong> Make sure to explore these new features and let us know if you encounter any issues.</p>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-primary" data-bs-dismiss="modal">Got it!</button>
		  </div>
		</div>
	  </div>
	</div>

	<!-- Optional JS to auto-show modal on page load -->
	<script>
	  document.addEventListener('DOMContentLoaded', function () {
		var whatsNewModal = new bootstrap.Modal(document.getElementById('whatsNewModal'));
		whatsNewModal.show();
	  });
	</script>

    <div class="row main-container">
        <div class="col-lg-6 p-4 d-flex flex-column gap-5">
            <!-- School Logo and Name -->
            <div>
                <a href="<?=  $link ?>" class="school d-flex align-items-center text-decoration-none">
                    <div>
                        <img src="../images/COLLEGE_LOGO-PNG.png" alt="FCPC logo" class="me-2 school-logo">
                    </div>
                    <div>
                        <p>First City Providential College</p>
                    </div>
                </a>
            </div>

            <!-- Login input fields -->
            <div class="flex-grow-1 d-flex flex-column justify-content-center align-items-center w-75 m-auto">
                <div>
                    <h1 class="system-name"><strong>School Portal</strong></h1>
                </div>

                <div class="alert w-50 border-1 text-center shadow-sm" role="alert" id="message">
                </div>

                <!-- Google Login -->
                <div id="google-container">

                    <p class="text-center text-secondary">
                        Login using your FCPC email to access your account.
                    </p>
                    <div class="m-auto text-center">
                        <a href="<?= $url ?>" class="btn-google btn btn-light border border-dark-subtle rounded-1 shadow-sm w-100 py-1" id="btnGoogleLogin">
                            <div class="d-flex align-items-center text-dark justify-content-center">
                                <img src="../images/google.png" alt="Google logo" height="20" class="m-2">
                                <span>Sign in with Google</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Manual Login -->
                <div id="manual-container">
                    <p class="text-center text-secondary">
                        Login using your username and password to access your account.
                    </p>
                    <div class="w-75 m-auto">
                        <label class="mb-1" for="username"><strong>Username:</strong></label>
                        <div class="mb-2">
                            <input type="text" id="username" class="form-control">
                            <small id="emailHelp" class="form-text text-muted">e.g. firstname.lastname@fcpc.edu.ph</small>
                        </div>

                        <label class="mb-1" for="userpassword"><strong>Password:</strong></label>
                        <div class="input-group">
                            <input type="password" id="userpassword" class="form-control" aria-describedby="view-password">
                            <button class="btn" style="background-color: #071976; color: white;" type="button" id="view-password"><i class="fa-solid fa-eye"></i></button>
                        </div>

                        <div class="mb-4 text-end">
                            <a id="btn-reset">Forgot Your Password?</a>
                        </div>

                        <div>
                            <button type="button" class="btn btn-primary w-100 shadow-sm" id="btnLogin">Login</button>
                        </div>

                    </div>
                </div>

                <!-- Reset Password -->
                <div id="reset-container">
                    <p class="text-center text-secondary">
                        Enter your FCPC email to reset your manual account password.
                    </p>
                    <div class="w-75 m-auto">
                        <label class="mb-1" for="email"><strong>Email:</strong></label>
                        <div class="mb-2">
                            <input type="text" id="email" class="form-control">
                            <small id="emailHelp" class="form-text text-muted">e.g. firstname.lastname@fcpc.edu.ph</small>
                        </div>

                        <!-- Google reCAPTCHA widget -->
                        <div class="g-recaptcha d-flex justify-content-center m-4" data-sitekey="6LfweHorAAAAAMxCvkqiqCSzez41nNG1_pnupWpP"></div>

                        <div>
                            <button type="button" class="btn btn-primary w-100 shadow-sm" id="btnReset">Send Reset Link</button>
                        </div>

                    </div>

                </div>

                <!-- Method Switch -->
                <div class="w-75">
                    <div class="line-text text-secondary">Or Sign in with</div>
                    <div class="d-flex justify-content-center gap-2">
                        <button id="btn-manual" class="btn-pill btn border border-secondary shadow-sm rounded-pill px-4">
                            <div class="d-flex align-items-center justify-content-center ">
                                <img src="../images/text-box.png" alt="Textbox Icon" height="20" class="m-2">
                                <span>Manual</span>
                            </div>
                        </button>
                        <button id="btn-google" class="btn-pill btn border border-secondary shadow-sm rounded-pill px-4">
                            <div class="d-flex align-items-center justify-content-center ">
                                <img src="../images/google.png" alt="Google logo" height="20" class="m-2">
                                <span>Google</span>
                            </div>
                        </button>
                    </div>
                    <div class="card bg-warning-subtle mt-4 shadow-sm text-center">
                        <div class="card-body">
                            <h5 class="card-title text-warning-emphasis"><?= $title_text; ?></h5>
                            <p class="card-text"><?= $content_text; ?></p>
                            <a href="../onlineregistration/" class="m-2 btn btn-warning <?= $content_text === 'No enrollment ongoing.' ? 'd-none' : '' ?>">New Student</a>
                            <a id="old-enrollment" class="m-2 btn btn-warning <?= $content_text === 'No enrollment ongoing.' ? 'd-none' : '' ?>">Old Student</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="d-flex justify-content-between">
                <span class="text-secondary">&copy; 2025 First City Providential College Inc. All rights reserved.</span>
                <span>
                    <a class="text-secondary text-decoration-none" data-bs-toggle="modal" data-bs-target="#policy-modal"> Privacy Policy |</a>

                    <a href="https://schoolportal.fcpc.edu.ph/faq.php" class="text-secondary text-decoration-none"> FAQs </a>
                </span>
            </div>
        </div>

        <div class="div-announcement col-lg-6 d-flex">
            <!-- <div id="carouselExampleAutoplaying" class="carousel slide m-auto carousel-fade" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="../images/carousel/1.jpg" class="d-block w-100" alt="..." height="500">
                    </div>
                    <div class="carousel-item">
                        <img src="../images/carousel/2.jpg" class="d-block w-100" alt="..." height="500">
                    </div>
                    <div class="carousel-item">
                        <img src="../images/carousel/3.jpg" class="d-block w-100" alt="..." height="500">
                    </div>
                    <div class="carousel-item d-flex justify-content-center align-items-center">
                        <img src="../images/carousel/4.png" class="img-fluid" alt="Carousel Image" height="500" width="500">
                    </div>
                    <div class="carousel-item">
                        <img src="../images/carousel/5.png" class="img-fluid" alt="..." height="500" width="500">
                    </div>
                    <div class="carousel-item">
                        <img src="../images/carousel/6.png" class="img-fluid" alt="..." height="500" width="500">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div> -->
        </div>

        <!-- Modal -->
        <div class="modal fade" id="policy-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <img src="../images/COLLEGE_LOGO-PNG.png" alt="FCPC logo" class="me-2 school-logo">
                        <h1 class="modal-title fs-4"> School Portal's Privacy Policy </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <style>
                            .policy-text {
                                text-indent: 2.5rem;
                                text-align: justify;
                            }
                        </style>
                        <p class="policy-title fs-5 d-none">
                            Our Statement
                        </p>
                        <p class="policy-text lh-lg">
                            To ensure full compliance with Republic Act No. 10173, otherwise known as the <i>Data Privacy Act of 2012</i>, 
                            FIRST CITY PROVIDENTIAL COLLEGE, INC. adopts this Privacy Policy to explain how the school collects, uses, stores, and 
                            protects the personal data you provide through this portal. 
                        </p>
                        <p class="policy-text lh-lg">
                            We are committed to safeguarding your privacy and ensuring 
                            that all personal and sensitive information you submit while accessing this system is kept secure. All data collected 
                            will be processed only for legitimate academic and administrative purposes and in accordance with this Policy and applicable laws.
                        </p>
                        </div>
                </div>
            </div>
        </div>

    </div>
</body>

</html>

<script src="../js/custom/login-script.js?t=<?= time() ?>"></script>