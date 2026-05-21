<?php

function getClientIpAddress(): string {
    $sources = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];

    foreach ($sources as $source) {
        if (!empty($_SERVER[$source])) {
            $ipList = explode(',', (string)$_SERVER[$source]);
            $ip = trim($ipList[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }

    return '0.0.0.0';
}

function logTadiActivity(mysqli $dbConn, string $access, ?string $error, int $userId, int $userType): void {
    $ipAddress = getClientIpAddress();
    $errorText = $error !== null ? trim($error) : '';

    $stmt = $dbConn->prepare(
        "INSERT INTO schooltadi_log (ip_address, access, error, user_id, user_type) VALUES (?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        return;
    }

    $stmt->bind_param("sssii", $ipAddress, $access, $errorText, $userId, $userType);
    $stmt->execute();
    $stmt->close();
}
