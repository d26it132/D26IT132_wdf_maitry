<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/db_connect_mysqli.php";

if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"])
) {
    header("Location: ../pages/login.php");
    exit;
}

if ($_SESSION["role"] !== "admin") {
    http_response_code(403);
    exit("Access denied. Admin access is required.");
}

$userId = (int) $_SESSION["user_id"];

$sql = "SELECT role, status
        FROM users
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();

if (
    !$user ||
    $user["role"] !== "admin" ||
    $user["status"] !== "active"
) {
    session_unset();
    session_destroy();

    header("Location: ../pages/login.php");
    exit;
}
?>