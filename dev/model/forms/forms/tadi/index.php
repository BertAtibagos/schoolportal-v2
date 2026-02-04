<?php 
    if(isset($_SESSION['EMPLOYEE']['ID'])){
        if(str_contains($_SESSION['EMPLOYEE']['INFO'], 'HR STAFF') || str_contains($_SESSION['EMPLOYEE']['INFO'], 'FINANCE')){
            include_once 'humanresource/index.php';
        }else{
            include_once 'prof/index.php';
        }
    } elseif(isset($_SESSION['STUDENT']['ID'])){
        include_once 'student/index.php';
    }
?>