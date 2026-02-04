<?php
session_start();

if (session_status() === PHP_SESSION_NONE || !isset($_SESSION['FULL_NAME'])) {
    session_unset();
    session_destroy();
    header("Location: ../../../login-model.php");
}

$session_stud = isset($_SESSION['STUDENT']) ? $_SESSION['STUDENT'] : 0;
$session_emp = isset($_SESSION['EMPLOYEE']) ? $_SESSION['EMPLOYEE'] : 0;

?>