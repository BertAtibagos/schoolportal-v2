<?php
    if(empty($_POST)){
        http_response_code(404);
        exit;
    }

    // PHP error handling
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);

    function isValidToken($token) {
        return is_string($token) && preg_match('/^[a-f0-9]{64}$/', $token);
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

    require_once 'configuration/connection-config.php';

    // Agent
    $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    $userAgent = json_encode([
        "IP" =>  filter_var($ip, FILTER_VALIDATE_IP),
        "AGENT" => getBrowserName($_SERVER['HTTP_USER_AGENT'])
    ]);

    $userAgent  = str_replace('"', "'", $userAgent);

    try {
        // === 1. Validate token ===
        $token = isValidToken($_POST['token']) ? htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8') : throw new Exception("Invalid token.");

        $stmt = $dbConn->prepare("SELECT `SysUserRstTkn_Email` FROM `systemuser_reset_tokens` tkn WHERE tkn.`SysUserRstTkn_Token` = ?;");
        $stmt->bind_param('s', $token);
        $stmt->execute();
                
        $stmt->bind_result($email);
        if (!$stmt->fetch()) { throw new Exception("Invalid token."); }
        $stmt->close();

        // === 2. Encrypt password ===
        $password = trim($_POST['password']);
        $password_hashed = $password ? password_hash($password, PASSWORD_ARGON2ID, $options) : exit('Missing Post');

        // === 3. Delete token from db ===
        $stmt = $dbConn->prepare("DELETE FROM `schoolportal_fcpc_edu_ph`.`systemuser_reset_tokens` WHERE `SysUserRstTkn_Token` = ?;");

        $stmt->bind_param('s', $token);
        $stmt->execute();
        $stmt->close();

        // === 4. Update password ===
        $stmt = $dbConn->prepare("UPDATE `schoolportal_fcpc_edu_ph`.`systemuser` SET `SysUser_PASSWORD` = ?
            WHERE `SysUser_STATUS` = 1 AND `SysUser_ISACTIVE` = 1 AND `SysUser_USERNAME` = ?;");

        $stmt->bind_param('ss', $password_hashed, $email);
        $stmt->execute();
        
        // === 5. Return Success ===
        // if($stmt->affected_rows == 1){ // causes error when multiple records are updated. 
        if($stmt->affected_rows > 0){ 
            $syntax = json_encode([
                "status" => "SUCCESS",
                "email" => $email,
                "new_password" => $password,
                "token" => $token,
                "agent" => $userAgent
            ]);
            
            $id = 0;
            $module = 'UPDATE PASSWORD';
            $operation = 'UPDATE SCHOOL PORTAL PASSWORD';
            
            $stmt = $dbConn->prepare("CALL `spInsertLogs`(?, ?, ?, ?, 'systemuser')");
            $stmt->bind_param("isss", $id, $module, $operation, $syntax);
            $stmt->execute();

            echo json_encode(["success" => true, "email" => $email, "password" => $password]);
        } else {
            echo json_encode(["error" => true, "message" => "An error occurred. Please contact the ICT Department promptly."]);
        }

        $stmt->close();
        
    } catch (Throwable $e) {
        // Don't echo raw error to the browser
        // You can log it instead: error_log($e->getMessage());
        error_log($e->getMessage());
        exit(json_encode(array('error'=>'An error occurred. Please try again later.')));
    }
?>