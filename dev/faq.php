<?php
// $link = "http://localhost/schoolportal";
$link = "https://schoolportal.fcpc.edu.ph";

$faqs = [
    [
        "question" => "How do I log in to the School Portal?",
        "answer" => "To log in to the FCPC School Portal, use the credentials sent to your personal or official FCPC email address. 
        Please check your Gmail inbox and ensure that you are logged in to the correct email account. You may also search for the keyword <strong>“school portal”</strong> in Gmail. <br><br>
        If you did not receive an email containing your credentials, you may use the <strong>“Forgot Password”</strong> feature on the <a href='$link' target='_blank'>School Portal Login Page</a>. 
        Please note that you must use your <strong>official FCPC email address</strong> to reset your password."
    ],
    [
        "question" => "Where can I find my login credentials?",
        "answer" => "Your login credentials are sent to either your personal email address or your official FCPC email address after enrollment. <br><br>
        If you cannot locate the email, you may use the <strong>“Forgot Password”</strong> feature on the <a href='$link' target='_blank'>School Portal Login Page</a>."
    ],
    [
        "question" => "How do I receive my login credentials?",
        "answer" => "To receive your login credentials, you must complete the entire enrollment process for the current semester, starting from Step 1 up to the final step. <br><br>
        Once enrollment is completed, the FCPC ICT Department will send your credentials to your personal or official FCPC email address."
    ],
    [
        "question" => "How can I reset my password?",
        "answer" => "You may reset your School Portal password by using the <strong>“Forgot Password”</strong> feature on the <a href='$link' target='_blank'>School Portal Login Page</a>. <br><br>
        Please ensure that you use your <strong>official FCPC email address</strong> when requesting a password reset."
    ],
    [
        "question" => "Why can't I access the Academics Module in the School Portal?",
        "answer" => "If the Academics Module is not visible in your menu, you must first complete all required surveys and evaluations in the <strong>Survey Module</strong>. <br><br>
        After completing all surveys, please log out of the portal and log in again for the changes to take effect."
    ],
    [
        "question" => "Why can't I see my grades even though I can access the Academics Module?",
        "answer" => "The Academics Module displays all your enrolled subjects and their corresponding grades. 
        If your grades are not visible, please check the <strong>“Status”</strong> column to see the current grade status. <br><br>
        Only <strong>registrar-approved grades</strong> are visible to students. Grades that are pending or not yet approved will not be displayed."
    ],
    [
        "question" => "Who can I contact for technical issues?",
        "answer" => "If you continue to experience technical issues after following the suggested solutions above, you may contact the FCPC ICT Department through our <a href='https://facebook.com/fcpc.ict' target='_blank'>official Facebook Page</a> or visit the ICT Office during regular working hours."
    ]
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>School Portal | FAQs</title>
    <link rel="icon" href="dev/images/fcpc_logo.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/fontawesome/fontawesome.min.css">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../assets/bootstrap/bootstrap.min.css">
    <script src="../assets/bootstrap/bootstrap.bundle.min.js"></script>

    <!-- JQuery -->
    <script src="../assets/jquery/jquery.min.js"></script>

    <link rel="stylesheet" href="../css/custom/change-password-style.css?t=<?php echo time(); ?>" />
    <style>
        /* Remove default Bootstrap arrow */
        .accordion-button::after {
            display: none;
        }
        /* Animate icon */
        .accordion-button i {
            transition: transform 0.3s;
        }
        .accordion-button:not(.collapsed) i {
            transform: rotate(90deg);
        }
        
        .color {
            color: #071976
        }

        .answer {
            margin-bottom: 0;
            text-indent: 2.5rem;
        }
    </style>
</head>
<body>

<div class="main-container">
    <div class="header-container pb-4">
        <a href="<?= $link ?>" class="school d-flex align-items-center text-decoration-none">
            <div>
                <img src="../images/COLLEGE_LOGO-PNG.png" alt="FCPC logo" class="me-2 school-logo">
            </div>
            <div>
                <h5 class="school-name m-0">First City Providential College</h5>
            </div>
        </a>
    </div>
    <h4 class="text-center mb-4 color">Frequently Asked Questions</h4>

    <div class="accordion w-75" id="faqAccordion">
        <?php foreach ($faqs as $index => $faq): ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading<?= $index ?>">
                    <button class="accordion-button collapsed d-flex align-items-center"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse<?= $index ?>"
                            aria-expanded="false"
                            aria-controls="collapse<?= $index ?>">

                        <i class="fa-solid fa-chevron-right me-2 color"></i>
                        <p class='fw-medium mb-0 ms-1'><?= htmlspecialchars($faq['question']) ?></p>
                    </button>
                </h2>

                <div id="collapse<?= $index ?>"
                     class="accordion-collapse collapse"
                     aria-labelledby="heading<?= $index ?>"
                     data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <?= $faq['answer'] ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>

</body>
</html>
