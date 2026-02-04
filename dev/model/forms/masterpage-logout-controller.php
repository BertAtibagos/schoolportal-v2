<?php  

    // ✅ Secure cookie flags (must be set before session_start)
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.cookie_samesite', 'Strict');

    // PHP error handling
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);

	if(session_status() === PHP_SESSION_NONE){
		session_start();
	}

    if (!isset($_SESSION['FULL_NAME'])) {
        header('Location: ../login-model.php');
        exit();
    }

    function getBrowserName($userAgent) {
        $userAgent = strtolower($userAgent);
        if (strpos($userAgent, 'chrome') !== false && strpos($userAgent, 'edge') === false && strpos($userAgent, 'opr') === false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'safari') !== false && strpos($userAgent, 'chrome') === false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'edge') !== false) {
            return 'Edge';
        } elseif (strpos($userAgent, 'opr') !== false || strpos($userAgent, 'opera') !== false) {
            return 'Opera';
        } else {
            return 'Other';
        }
    }

    require_once '../configuration/connection-config.php';
    if (isset($_SESSION['EMPLOYEE']) && isset($_SESSION['STUDENT'])) {
        $usertype = "Employee & Student";
    } elseif (isset($_SESSION['EMPLOYEE'])) {
        $usertype = "Employee";
    } elseif (isset($_SESSION['STUDENT'])) {
        $usertype = "Student";
    }

    // Email & ID/s
    $id = 0;
    $email = isset($_SESSION['EMAIL_ADDRESS']) ? $_SESSION['EMAIL_ADDRESS'] : '';
    $operation = strtoupper($usertype) . " USER LOGOUT";

    // Agent
    $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    $userAgent = json_encode([
        "IP" =>  filter_var($ip, FILTER_VALIDATE_IP),
        "AGENT" => getBrowserName($_SERVER['HTTP_USER_AGENT'])
    ]);
    $userAgent  = str_replace('"', "'", $userAgent);

    $syntax = json_encode([
        "status" => "SUCCESS",
        "email" => $email,
        "agent" => $userAgent
    ]);

    $stmt = $dbConn->prepare("CALL `spInsertLogs`(?, 'LOGOUT', ?, ?, 'systemuser')");
    $stmt->bind_param("iss", $id, $operation, $syntax);
    $stmt->execute();

	//clearstatcache();
	session_destroy();
	header("Location: ../../index.php");
	exit();
?>