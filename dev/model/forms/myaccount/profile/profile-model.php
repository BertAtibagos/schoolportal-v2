<?php
    include_once "../partials/user_image_loader.php";

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

<style>
    .avatar {
        width: 120px;
        /* change to suit */
        height: 120px;
        /* same as width for perfect circle */
        overflow: hidden;
        border: 5px solid #fff;
        /* white border */
        border-radius: 50%;
    }

    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* crops to fill container */
        display: block;
    }

    
    @media screen and (orientation: portrait) {
        .student-info {
            text-align: center;
        }
    }   
</style>

<?php include '../partials/loader.php';?>
<section id="section-profile">
    <div class="row align-items-center">
        <div class="col-md-2 d-flex justify-content-center py-3">
            <div class="avatar shadow">
                <img src="<?= getUserImage()?>" alt="Profile photo" class="img-fluid rounded-circle">
            </div>
        </div>
        <div class="col-md-10 p-3 student-info">
            <p class="mb-0"><strong><?= $_SESSION['FULL_NAME']?></strong></p>
            <p class="text-secondary"><span id="user_idno">--</span>, <?= $usertype ?></p>
            <!-- <p class="mb-0"><span id="user_info">--</span> (<span id="user_section">--</span>)</p> -->
            <p class="mb-0"><?= generateUserInfo()?></p>

        </div>
    </div>
    <div class="p-3">
        <h5 class="pb-3">Contact Information</h5>
        <div class="row border rounded border-secondary-subtle shadow-sm p-3">
            <div class="col-lg-4">
                <p class="mb-0 text-secondary">Email:</p>
                <p class="fw-medium" id="user_email"><?= $_SESSION['EMAIL_ADDRESS'] ?></p>
            </div>
            <div class="col-lg-4">
                <p class="mb-0 text-secondary">Mobile:</p>
                <p class="fw-medium" id="user_mobile">--</p>
            </div>
            <div class="col-lg-4">
                <p class="mb-0 text-secondary">Telephone:</p>
                <p class="fw-medium" id="user_telephone">--</p>
            </div>
        </div>
    </div>
    <div class="p-3">
        <h5 class="pb-3">Personal Information</h5>
        <div class="row border rounded border-secondary-subtle shadow-sm p-3">
            <div class="col-lg-4">
                <div>
                    <p class="mb-0 text-secondary">Gender:</p>
                    <p class="fw-medium" id="user_gender"><?= ucwords(strtolower($_SESSION['GENDER'])) ?></p>
                </div>
                <div>
                    <p class="mb-0 text-secondary">Date of birth:</p>
                    <p class="fw-medium" id="user_birthdate">--</p>
                </div>
                <div>
                    <p class="mb-0 text-secondary">Nationality:</p>
                    <p class="fw-medium" id="user_nationality">--</p>
                </div>
                <div>
                    <p class="mb-0 text-secondary">Religion:</p>
                    <p class="fw-medium" id="user_religion">--</p>
                </div>
            </div>
            <div class="col-lg-8">
                <div>
                    <p class="mb-0 text-secondary">Present Address:</p>
                    <p class="fw-medium" id="user_present_add">--</p>
                </div>
                <div>
                    <p class="mb-0 text-secondary">Permanend Address:</p>
                    <p class="fw-medium" id="user_permanent_add">--</p>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="module" src="<?= assetLoader('../../js/custom/profile-script.js') ?>"></script>