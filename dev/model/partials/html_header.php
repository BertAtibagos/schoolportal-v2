<?php
    function assetLoader($path) {
        // $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($path, '/');
        $fullPath = $path;
        $version = file_exists($fullPath) ? filemtime($fullPath) : time();
        // return $fullPath . '?v=' . $version;
		return $fullPath . '?v=1';
    }

    function generateUserInfo(){
        $isEmployee = isset($_SESSION['EMPLOYEE']['ID']) && isset($_SESSION['EMPLOYEE']['INFO']);
        $isStudent = isset($_SESSION['STUDENT']['ID']) && isset($_SESSION['STUDENT']['INFO']);

        return 
            ($isEmployee ? $_SESSION['EMPLOYEE']['INFO'] : '') .
            ($isEmployee && $isStudent ? ' & ' : '') .
            ($isStudent ? $_SESSION['STUDENT']['INFO'] : '');
    }

    // PHP error handling
    // ini_set('display_errors', 0);
    // ini_set('log_errors', 1);
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCPC School Portal</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="FCPC School Portal provides students with secure access to grades, tuition fees, enrollment, and surveys online.">
    <meta name="keywords" content="FCPC, School Portal, Student Portal, Online Enrollment, Academic Records">
    <meta name="author" content="FCPC ICT Department">

    <!-- Favicon & App Icons -->
    <link rel="icon" href="../../images/fcpc_logo.ico" sizes="32x32">
    <link rel="apple-touch-icon" href="../../images/fcpc_logo.png">

    <!-- Preconnect for faster asset loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

    <!-- CSS Bootstrap & FontAwesome -->
    <link href="<?= assetLoader('../../assets/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <script src="<?= assetLoader('../../assets/bootstrap/bootstrap.bundle.min.js') ?>"></script>

    <link href="<?= assetLoader('../../assets/fontawesome/fontawesome.min.css') ?>" rel="stylesheet">
    <script src="<?= assetLoader('../../assets/fontawesome/fontawesome.min.js') ?>"></script>

    <!-- JQuery -->
    <script src="<?= assetLoader('../../assets/jquery/jquery.min.js') ?>"></script>

    <!-- Grid.js JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/gridjs/dist/gridjs.umd.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" /> -->

    <!-- Custom CSS -->
    <link href="<?= assetLoader('../../css/custom/masterpage-style.css') ?>" rel="stylesheet">
</head>