<?php
    include_once 'user_image_loader.php';
?>

<div id="divHeader" class="d-flex align-items-center">
    <div class="me-2" id="imgContainer">
        <button id="btnImage" class="btn btn-link p-0">
            <img src="<?= getUserImage() ?>" class="img-fluid rounded-circle" alt="Student Image">
        </button>
    </div>
    <div>
        <strong id="txtStudName"><?= $_SESSION['FULL_NAME']?></strong>
        <p id="txtStudInfo"><?= generateUserInfo()?></p>
    </div>
    <div id="btnLogoutContainer">
        <a class="btn btn-link link-warning d-flex align-items-center" href="masterpage-logout-controller.php">
            <div class="text-end"><i class="fa fa-arrow-right-from-bracket"></i></div>
        </a>
    </div>
</div>