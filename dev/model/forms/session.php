<?php 
    session_start();

    if (!isset($_SESSION['FULL_NAME'])) {
        session_unset();
        session_destroy();
        header("Location: ../login-model.php");
    }

    // If page is passed, store it (raw) – validation happens in masterpage-model.php
    if (isset($_GET['page'])) {
        $_SESSION['page'] = $_GET['page'];
    } else {
        // Optional: default page
        $_SESSION['page'] = 'dashboard';
    }

    header("Location: masterpage-model.php");
    exit();
?>