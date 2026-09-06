<?php
function checkMaintenanceMode(): bool
{
    $configPath = __DIR__ . '/../../../configuration/maintenance.json';

    if (!file_exists($configPath)) {
        return false; // fail-open: no config means site stays up
    }

    $config = json_decode(file_get_contents($configPath), true);

    if (empty($config['enabled'])) {
        return false;
    }

    // Bypass for allowed IPs
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? '';
    if (in_array($clientIp, $config['allowed_ips'] ?? [], true)) {
        return false;
    }

    // Bypass for allowed roles (e.g. admins can still log in and toggle it off)
    $sessionRole = $_SESSION['EMPLOYEE']['role'] ?? $_SESSION['STUDENT']['role'] ?? null;
    if ($sessionRole && in_array($sessionRole, $config['bypass_roles'] ?? [], true)) {
        return false;
    }

    http_response_code(503);
    require __DIR__ . '/maintenance.php';
    return true;
}
?>