<?php
function getUserImage(): string {
    // $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    // $host     = $_SERVER['HTTP_HOST'];
    // $basePath = dirname($_SERVER['SCRIPT_NAME']);

    // $baseURL = $protocol . '://' . $host . $basePath;

    // Default gender fallback from session
    $gender = strtolower($_SESSION['GENDER'] ?? 'male');
    $default = "../../images/$gender.png";

    $link = $default; // start with default

    /* STUDENT */
    if (!empty($_SESSION['STUDENT']['ID'])) {
        $file_name = str_replace('-', '_', (string) $_SESSION['STUDENT']['IDNO']);
        $custom = "../../public/users/student/images/" . $file_name . ".jpg";

		if (file_exists($custom)) {
			$link = $custom;
		}
    }
	
    /* EMPLOYEE */
    if (!empty($_SESSION['EMPLOYEE']['IDNO'])) {
        $file_name = str_replace('-', '_', (string) $_SESSION['EMPLOYEE']['IDNO']);
        $custom = "../../public/users/employee/images/" . $file_name . ".jpg"; 

		if (file_exists($custom)) {
			$link = $custom;
		}    
    }
    
    return $link;
}
