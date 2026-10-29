<?php

function logAudit($conn, $userId, $action, $details = "")
{
    $ipAddress = $_SERVER["REMOTE_ADDR"] ?? "Unknown";

    $sql = "INSERT INTO audit_logs
            (user_id, action, details, ip_address)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "isss",
        $userId,
        $action,
        $details,
        $ipAddress
    );

    $result = $stmt->execute();

    $stmt->close();

    return $result;
}
?>