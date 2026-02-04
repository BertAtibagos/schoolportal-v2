<?php 
    date_default_timezone_set('Asia/Manila');
    
    function validFCPCEmail($str){
        $email = filter_var($str, FILTER_SANITIZE_EMAIL);
        
        if(!str_contains($str, '@')){ 
            return false;
        }

        $user = explode('@', $email)[0];
        $domain = explode('@', $email)[1];

        $domainList = array("fcpc-inc.com", "fcpc.edu.ph");
        // $domainList = array("fcpc.com.ph", "fcpc-inc.com", "fcpc.edu.ph");

        if($domain && in_array($domain, $domainList) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        return false;
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

	require_once 'configuration/connection-config.php';
    require '../components/vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $type = isset($_POST['type']) ? strtoupper(trim($_POST['type'])) : '';
    
    // Agent
    $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    $userAgent = json_encode([
        "IP" =>  filter_var($ip, FILTER_VALIDATE_IP),
        "AGENT" => getBrowserName($_SERVER['HTTP_USER_AGENT'])
    ]);

    $userAgent  = str_replace('"', "'", $userAgent);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $type === 'LOGIN') {
        if (isset($_POST['uemail']) && isset($_POST['upass'])){
            
            // select account using email, return type
            $email = $_POST['uemail'] ? validFCPCEmail($_POST['uemail']) : '';

            if ($email == '' || !$email){
                echo json_encode(['error' => true, "message" => "Invalid FCPC Email."]);
                exit;
            }

            $password = trim($_POST['upass']);
            $password_hashed = $password ? password_hash($password, PASSWORD_ARGON2ID, $options) : exit('Missing Post.');

            $stmt = $dbConn->prepare("SELECT us.`SysUser_PASSWORD` `PASSWORD`, us.`SysUserType_ID` `TYPE` FROM `systemuser` us 
                WHERE us.`SysUser_STATUS` = 1
                AND us.`SysUser_ISACTIVE` = 1
                AND us.`SysUser_USERNAME` = ?;");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            $usertype = 0;
            $userpassword = '';

            while ($row = $result->fetch_assoc()) {
                $passwordIsValid = password_verify($password, $row['PASSWORD']);
                if($passwordIsValid) { 
                    $usertype = intval($row['TYPE']);
                    $userpassword = trim($row['PASSWORD']);
                }
            }

            // echo $userpassword;

            $stmt->close();

            $status = 0;
            $stmt = $dbConn->prepare("CALL `spLoginUser_NEW`(?, ?, 'manual', ?)");
            $stmt->bind_param("sss", $email, $userpassword, $userAgent);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->field_count == 19) { // expecting 19 columns from query return if success.
                $employeeRow = null;
                $studentRow = null;

                while ($row = $result->fetch_assoc()) {
                    if ($row['TYPE'] === "EMPLOYEE") {
                        $employeeRow = $row;
                    }

                    if ($row['TYPE'] === "STUDENT") {
                        $studentRow = $row;
                    }

                    $_SESSION[$row['TYPE']] = [
                        "ID" => $row['ID'],
                        "IDNO" => $row['IDNO'],
                        "INFO" => $row['INFO'],
                        "LVLID" => $row['LEVEL'],
                        "YRID" => $row['YEAR'],
                        "PRDID" => $row['PERIOD'],
                        "YRLVLID" => $row['YEAR_LEVEL'],
                        "CRSEID" => $row['COURSE'],
                        "SECID" => $row['SECTION'],
                        "DEPID" => $row['DEPARTMENT'],
                        "ACCESS_RIGHTS" => $row['ACCESS_RIGHTS'],
                        "CATEGORY" => $row['CATEGORY']
                    ];
                }

                // // Flush remaining results to execute the INSERT inside SP
                // while ($stmt->more_results() && $stmt->next_result()) {
                //     // No need to fetch anything, just flush
                // }

                // Prioritize employee info, fallback to student if no employee found
                $chosen = $employeeRow ?: $studentRow;

                if ($chosen) {
                    $_SESSION['LAST_NAME'] = $chosen['LAST_NAME'];
                    $_SESSION['FIRST_NAME'] = $chosen['FIRST_NAME'];
                    $_SESSION['MIDDLE_NAME'] = $chosen['MIDDLE_NAME'];
                    $_SESSION['SUFFIX'] = $chosen['SUFFIX'];
                    $_SESSION['GENDER'] = $chosen['GENDER'];
                    $_SESSION['EMAIL_ADDRESS'] = $chosen['EMAIL_ADDRESS'];
                    $_SESSION['FULL_NAME'] = $chosen['LAST_NAME'] . ", " . $chosen['FIRST_NAME'] . " " . $chosen['MIDDLE_NAME'] . " " . $chosen['SUFFIX'];
                    $_SESSION['LOGIN_TIME'] = date('F j, Y (h:i A)');
                }
                
                echo json_encode(['success' => true, "message" => "Login Successful."]);
            } else {
                if ($fetch = $result->fetch_assoc()) {
                    $value = match($status) {
                        0 => "Invalid username or password.",
                        2 => "This account has been deleted. Please contact the ICT Department promptly.",
                        default => "This account has been disabled. Please contact the ICT Department promptly.",
                    };

                    echo json_encode(['error' => true, "message" => $value]);
                }
            }

            $result->free(); 
            $stmt->close();
        } 
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $type === 'PASSWORD_RESET') {
        if(isset($_POST['email']) && isset($_POST['g-recaptcha-response'])) {
            try {
                // === 1. Validate CAPTCHA ===
                $recaptchaSecret = RECAPTCHA_SECRET;
                $recaptchaResponse = $_POST['g-recaptcha-response'];

                $verifyResponse = file_get_contents(
                    "https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}"
                );
                $responseData = json_decode($verifyResponse);

                if (!$responseData->success) {
                    throw new Error('CAPTCHA verification failed.');
                }

                // === 2. Validate Email and user type ===
                $email = validFCPCEmail(trim($_POST['email']));

                if (!$email) {
                    throw new Error("Invalid FCPC Email."); 
                }

                // === 3. Get and check Attempts ===
                $ip_address = $_SERVER['REMOTE_ADDR'];
                $one_hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));
                $stmt = $dbConn->prepare('SELECT COUNT(*) FROM `systemuser_reset_attempts` rst
                    WHERE rst.`SysUserRstAtmpt_IP_Address` = ?
                    AND rst.`SysUserRstAtmpt_Time` > ?');

                $stmt->bind_param('ss', $ip_address, $one_hour_ago);
                $stmt->execute();
                
                $stmt->bind_result($attempts);
                $stmt->fetch();
                $stmt->close();

                if ($attempts >= 3){
                    throw new Error("Too many attempts. Please try again later.");
                }

                // === 4. Insert Attempt ===
                $stmt = $dbConn->prepare("INSERT INTO `schoolportal_fcpc_edu_ph`.`systemuser_reset_attempts` (
                    `SysUserRstAtmpt_IP_Address`,
                    `SysUserRstAtmpt_Time`
                ) VALUES (?, NOW()) ;");
                $stmt->bind_param('s', $ip_address);
                $stmt->execute();
                $stmt->close();

                // === 5. Get user by email ===
                $stmt = $dbConn->prepare("SELECT us.`SysUser_ID` `ID` FROM `systemuser` us 
                    WHERE us.`SysUser_ISACTIVE` = 1 
                    AND us.`SysUser_STATUS` = 1
                    AND us.`SysUser_USERNAME`  = ?;");
                
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $stmt->store_result();
                
                if ($stmt->num_rows >= 1 && $stmt->num_rows <= 2) {
                    $token = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

                    // === 6. Delete previous tokens ===
                    $stmt = $dbConn->prepare("DELETE FROM
                    `schoolportal_fcpc_edu_ph`.`systemuser_reset_tokens` 
                    WHERE `SysUserRstTkn_Email` = ?;");

                    $stmt->bind_param('s', $email);
                    $stmt->execute();
                    $stmt->close();
                    
                    // === 7. Insert attempt and token to Db ===
                    $stmt = $dbConn->prepare("INSERT INTO `schoolportal_fcpc_edu_ph`.`systemuser_reset_tokens` (
                        `SysUserRstTkn_Email`,
                        `SysUserRstTkn_Token`,
                        `SysUserRstTkn_Expiration`
                        ) VALUES (?, ?, ?);");

                    $stmt->bind_param('sss', $email, $token, $expires);
                    $stmt->execute();
                    $stmt->close();
                    

                    // === 8. Prepare email variables ===
                    $reset_link = "https://schoolportal.fcpc.edu.ph/model/change-password.php?token=$token";
                    // $reset_link = "http://localhost/schoolportal/dev/model/change-password.php?token=$token";
                    
                    // === 8.5 Log Activity ===
                    $syntax = json_encode([
                        "status" => "SUCCESS",
                        "email" => $email,
                        "token" => $token,
                        "agent" => $userAgent
                    ]);
                    $id = 0;
                    $module = 'REQUEST PASSWORD RESET';
                    $operation = 'REQUEST FOR SCHOOL PORTAL PASSWORD RESET';
                    
                    $stmt = $dbConn->prepare("CALL `spInsertLogs`(?, ?, ?, ?, 'systemuser')");
                    $stmt->bind_param("isss", $id, $module, $operation, $syntax);
                    $stmt->execute();
                    $stmt->close();
                    
                    // === 9. Send email na bih ===
                    $mail = new PHPMailer(true);

                    // SMTP Server settings
                    $mail->isSMTP();
                    $mail->Host = SMTP_HOST;
                    $mail->SMTPAuth = true;
                    $mail->Username = SMTP_USER;
                    $mail->Password = SMTP_PASS;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = SMTP_PORT;

                    // Sender and recipient settings
                    $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
                    // $mail->addAddress("ramonjoseph.verdon@fcpc.edu.ph", "");
                    $mail->addAddress($email, "");

                    // Email content
                    $mail->isHTML(true);
                    $mail->Subject = 'FCPC School Portal: Password Reset Request';
                    $mail->Body = "<!DOCTYPE html>
                                    <html> 
                                        <head>
                                            <meta charset='UTF-8'>
                                        </head>
                                        <body style='font-family: Arial, sans-serif;'>
                                            <img width='85'; height='100'; src='https://firebasestorage.googleapis.com/v0/b/emailing-header-footer.appspot.com/o/FCPC-LOGO-2.png?alt=media&token=15f71c79-c5ba-469e-8f67-b5066e07ad1f' alt='FCPC'>
                                            <br>
                                            <p align='justify'>
                                                <p>Hi! 👋</p>

                                                <p>We received a request to reset your password <span role='img' aria-label='key'>🔑</span>.</p>

                                                <p>No worries — you can create a new one by clicking the button below:</p>

                                                <p>
                                                    <a href='$reset_link' 
                                                        class='button'
                                                        style='background-color: #071976; color: #ffffff; padding: 8px 16px; text-decoration: none; border-radius: 6px; display: inline-block;'>
                                                        Reset Password
                                                    </a>
                                                </p>
                                                <p>or</p>
                                                <p>
                                                    Copy and paste this link in your browser: <br>
                                                    <a href='$reset_link' style='color: #071976;' >$reset_link</a>
                                                </p>

                                                <p style='font-size: 14px;'>⏳ This link will expire in <strong>30 minutes</strong> for your security.</p>

                                                <p>🚫 If you did not request a password reset, you can safely ignore this email.</p>

                                                <hr style='margin: 20px 0; border: none; border-top: 1px solid #ddd;'>
                                                
                                                <p style='color: #d93025; font-weight: bold;'>IMPORTANT:</p>
                                                <p style='font-size: 14px;'>
                                                    If this email is in your Spam folder, please mark it as 
                                                    <strong style='color: #071976;'>'Not Spam'</strong> or 
                                                    <strong style='color: #071976;'>'Report as NOT Spam'</strong> to ensure you receive future messages. 
                                                </p>
												<p style='font-size: 14px;'>
                                                    This action will only reset your School Portal manual account and <strong style='color: #071976;'>NOT reset</strong> the password for your FCPC Google Account. 
                                                </p>

                                                <br>

                                                <p style='font-size: 13px; color: #888; font-style: italic;'>
                                                    ~~ This is an automatically generated email. Please do not reply to this message. ~~
                                                </p>

                                                <p>Best regards,<br><br>
                                                ICT Department<br>
                                                First City Providential College, Inc.</p>
                                            </p>
                                            <br>
                                            <img width='500'; height='85'; src='https://firebasestorage.googleapis.com/v0/b/emailing-header-footer.appspot.com/o/FCPC-Footer.png?alt=media&token=96c33c9b-bb06-4fb0-813b-8760ffd54783' alt='contact'>
                                        </body> 
                                    </html>";

                    // Send the email
                    $isSent = $mail->send();
                    if (!$isSent){
                        throw new Error("Email sending error. Please contact the ICT Department promptly.");
                    }

                } else {
                    throw new Error('Account not found.');
                }
            } catch (Throwable $e) {
                // Don't echo raw error to the browser
                // You can log it instead: error_log($e->getMessage());
                // error_log($e->getMessage());
                exit(json_encode(['error' => true, "message" => $e->getMessage()]));
            }
            
            exit(json_encode(['success' => true, "message" => "If this email is registered, you'll receive a reset link shortly."]));
        }

	}
    
    $dbConn->close();
    exit;
?>
