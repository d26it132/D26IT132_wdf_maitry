<?php
session_start();

// Admin access check
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['user_role']) ||
    $_SESSION['user_role'] !== 'admin'
) {
    header("Location: ../pages/login.html");
    exit;
}

require_once __DIR__ . "/db_connect_mysqli.php";

function redirectDelete($message, $type = 'success')
{
    header(
        "Location: event_management.php?message=" .
        urlencode($message) . "&type=" . urlencode($type)
    );
    exit;
}

// Allow deletion only through POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: event_management.php");
    exit;
}

// Validate event ID
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    redirectDelete('notfound', 'error');
}

// Retrieve poster filename before deleting the record
$stmt = $conn->prepare("SELECT poster FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$event = $result->fetch_assoc();

$stmt->close();

if (!$event) {
    redirectDelete('notfound', 'error');
}

$poster = $event['poster'];

// Delete event from database
$stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
$stmt->bind_param("i", $id);

$success = $stmt->execute();
$stmt->close();

if (!$success) {
    redirectDelete('dbfailed', 'error');
}

// Delete associated poster file, if present
if (
    !empty($poster) &&
    basename($poster) === $poster
) {
    $posterPath = __DIR__ . "/../uploads/event_posters/" . $poster;

    if (is_file($posterPath)) {
        unlink($posterPath);
    }
}

redirectDelete('deleted');
?>