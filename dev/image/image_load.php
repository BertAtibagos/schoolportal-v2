<?php
// pixel.php
// Requirements: PHP 7.4+, PDO extension
// Example URL: /pixel.php?u=TRACKINGID

// --- Simple config (move to separate file in production) ---
$dbDsn = 'mysql:host=46.21.150.116;dbname=schoolportal_fcpc_edu_ph;charset=utf8mb4';
$dbUser = 'schoolportal_fcpc_edu_ph_remote_user';
$dbPass = '6$Sf&i3@8!Nx5GcKYbjP';

// --- Helpers ---
function get_client_ip() {
    // prefer X-Forwarded-For if behind trusted proxy; sanitize
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    // if (strpos($ip, ',') !== false) $ip = explode(',', $ip)[0];
    return trim($ip);
}
function ip_to_bin($ip) {
    $b = @inet_pton($ip);
    return $b === false ? null : $b;
}

// --- Read and validate uid ---
$uid = $_GET['u'] ?? '';
$email = $_GET['e'] ?? '';
$action = $_GET['a'] ?? '';

// allow limited charset: alnum, -, _, =
if (!preg_match('/^[A-Za-z0-9\-\_=]{1,120}$/', $uid)) {
    $uid = 'unknown';
}

// --- Collect metadata ---
$ip = get_client_ip();
$ip_bin = ip_to_bin($ip);
$ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 2000);
$al = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '', 0, 100);
$ref = substr($_SERVER['HTTP_REFERER'] ?? '', 0, 2048);

// capture all headers (optional, truncated)
$headers = '';
foreach (getallheaders() as $k => $v) {
    $headers .= "$k: $v\n";
}
$headers = substr($headers, 0, 4000);

// --- If HEAD request, return headers only ---
if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
    header('Content-Type: image/gif');
    header('Content-Length: 43'); // size of the GIF below
    // prevent caching so you get fresh hits
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
    http_response_code(200);
    exit;
}

// --- Insert to DB (best-effort, non-blocking if DB fails) ---
try {
    $pdo = new PDO($dbDsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $sql = "INSERT INTO email_opens (uid, ip, user_agent, accept_language, referer, headers, email, action)
            VALUES (:uid, :ip, :ua, :al, :ref, :headers, :email, :action)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':uid', $uid, PDO::PARAM_STR);
    $stmt->bindValue(':ip', $ip_bin, PDO::PARAM_LOB);
    $stmt->bindValue(':ua', $ua, PDO::PARAM_STR);
    $stmt->bindValue(':al', $al, PDO::PARAM_STR);
    $stmt->bindValue(':ref', $ref, PDO::PARAM_STR);
    $stmt->bindValue(':headers', $headers, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':action', $action, PDO::PARAM_STR);
    $stmt->execute();
} catch (Throwable $e) {
    // Fail silently to avoid breaking email rendering; consider logging locally to file
    error_log("pixel.php db error: " . $e->getMessage());
}

// --- Send 1x1 GIF with anti-caching headers ---
$gif = base64_decode('R0lGODlhAQABAPAAAP///wAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==');
header('Content-Type: image/gif');
header('Content-Length: ' . strlen($gif));
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
header('X-Robots-Tag: noindex');
echo $gif;
exit;
