<?php
session_start();

require_once '../components/vendor/autoload.php';
require_once 'configuration/connection-config.php';


/* -------------------------------
   BASIC ERROR CONFIG
-------------------------------- */
ini_set('display_errors', 0); // hide from users
ini_set('log_errors', 1);
error_reporting(E_ALL);

/* -------------------------------
   HELPER
-------------------------------- */
function getBrowserName($userAgent) {
    $ua = strtolower($userAgent);
    return str_contains($ua, 'chrome') && !str_contains($ua, 'edge') ? 'Chrome' :
           (str_contains($ua, 'firefox') ? 'Firefox' :
           (str_contains($ua, 'safari') && !str_contains($ua, 'chrome') ? 'Safari' :
           (str_contains($ua, 'edge') ? 'Edge' :
           (str_contains($ua, 'opr') ? 'Opera' : 'Other'))));
}

/* -------------------------------
   MAIN FLOW
-------------------------------- */
try {

    /* 1️⃣ Validate OAuth callback */
    if (!isset($_GET['code'])) {
        throw new Exception('Missing OAuth authorization code.');
    }

    /* 2️⃣ Google Client Setup */
    $client = new Google\Client();
    $client->setClientId(CLIENT_ID);
    $client->setClientSecret(CLIENT_SECRET);
    $client->setRedirectUri(CLIENT_REDIRECT);
    // $client->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
    $client->addScope(['email', 'profile']);

    /* 3️⃣ Exchange Code for Token */
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        throw new Exception($token['error_description'] ?? 'Google token error');
    }

    if (!isset($token['access_token'])) {
        throw new Exception('Access token missing.');
    }

    $client->setAccessToken($token['access_token']);

    /* 4️⃣ Get Google User Info */
    $oauth = new Google\Service\Oauth2($client);
    $userinfo = $oauth->userinfo->get();

    if (empty($userinfo->email) || !filter_var($userinfo->email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid Google account email.');
    }

    /* 5️⃣ Prepare Login Data */
    $ip = $_SERVER['HTTP_CLIENT_IP']
        ?? $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['REMOTE_ADDR'];

    $userAgent = json_encode([
        'IP'    => filter_var($ip, FILTER_VALIDATE_IP),
        'AGENT' => getBrowserName($_SERVER['HTTP_USER_AGENT'] ?? '')
    ]);
    $userAgent = str_replace('"', "'", $userAgent);

    /* 6️⃣ Database Login */
    $qry = $dbConn->prepare("CALL spLoginUser_NEW(?, '', 'google', ?)");
    if (!$qry) {
        throw new Exception('Database prepare failed.');
    }

    $email = $userinfo->email;
    $qry->bind_param("ss", $email, $userAgent);
    $qry->execute();

    $result = $qry->get_result();
    if (!$result) {
        throw new Exception('Login procedure returned no result.');
    }

    if ($result->field_count !== 19) {
        throw new Exception('Invalid login response.');
    }

    /* 7️⃣ Session Assignment */
    $employeeRow = null;
    $studentRow  = null;

    while ($row = $result->fetch_assoc()) {
        if ($row['TYPE'] === 'EMPLOYEE') $employeeRow = $row;
        if ($row['TYPE'] === 'STUDENT')  $studentRow  = $row;

        $_SESSION[$row['TYPE']] = [
            'ID' => $row['ID'],
            'IDNO' => $row['IDNO'],
            'INFO' => $row['INFO'],
            'LVLID' => $row['LEVEL'],
            'YRID' => $row['YEAR'],
            'PRDID' => $row['PERIOD'],
            'YRLVLID' => $row['YEAR_LEVEL'],
            'CRSEID' => $row['COURSE'],
            'SECID' => $row['SECTION'],
            'DEPID' => $row['DEPARTMENT'],
            'ACCESS_RIGHTS' => $row['ACCESS_RIGHTS'],
            'CATEGORY' => $row['CATEGORY']
        ];
    }

    $chosen = $employeeRow ?: $studentRow;
    if (!$chosen) {
        throw new Exception('User record not found.');
    }

    $_SESSION['LAST_NAME']  = $chosen['LAST_NAME'];
    $_SESSION['FIRST_NAME'] = $chosen['FIRST_NAME'];
    $_SESSION['MIDDLE_NAME'] = $chosen['MIDDLE_NAME'];
    $_SESSION['SUFFIX'] = $chosen['SUFFIX'];
    $_SESSION['GENDER'] = $chosen['GENDER'];
    $_SESSION['EMAIL_ADDRESS'] = $chosen['EMAIL_ADDRESS'];
    $_SESSION['FULL_NAME'] =
        trim("{$chosen['LAST_NAME']}, {$chosen['FIRST_NAME']} {$chosen['MIDDLE_NAME']} {$chosen['SUFFIX']}");
    $_SESSION['LOGIN_TIME'] = date('F j, Y (h:i A)');

    /* 8️⃣ Cleanup */
    $qry->close();
    $dbConn->close();

    /* 9️⃣ Success Redirect */
	echo var_dump($_SESSION);
    header("Location: /model/forms/masterpage-model.php");
    exit;

} catch (Throwable $e) {

    /* 🔴 HTTP 500 HANDLER */
    error_log('[GOOGLE LOGIN ERROR] ' . $e->getMessage());

    http_response_code(500);
    echo "
        <h3 style='color:#b00020'>Login Failed</h3>
        <p>Please try signing in again.</p>
        <a href='/'>Return to Login</a>
    ";
    exit;
}
