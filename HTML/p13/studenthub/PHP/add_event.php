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

$uploadDir = __DIR__ . "/../uploads/event_posters/";

function redirectEvent($message, $type = 'success')
{
    header(
        "Location: event_management.php?message=" .
        urlencode($message) . "&type=" . urlencode($type)
    );
    exit;
}

function validateEventPoster($file)
{
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if ($file['size'] <= 0 || $file['size'] > 2 * 1024 * 1024) {
        return false;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($allowed[$mime])) {
        return false;
    }

    if (@getimagesize($file['tmp_name']) === false) {
        return false;
    }

    return $allowed[$mime];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['event_name'] ?? '');
    $date = trim($_POST['event_date'] ?? '');
    $venue = trim($_POST['venue'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $status = $_POST['status'] ?? 'Open';

    $dateObject = DateTime::createFromFormat('!Y-m-d', $date);

    $validDate = $dateObject &&
        $dateObject->format('Y-m-d') === $date;

    if (
        $name === '' ||
        $venue === '' ||
        !$validDate ||
        strlen($name) > 150 ||
        strlen($venue) > 200 ||
        strlen($category) > 100 ||
        !in_array($status, ['Open', 'Closed'], true)
    ) {
        redirectEvent('invalid', 'error');
    }

    $extension = validateEventPoster($_FILES['poster'] ?? null);

    if ($extension === false) {
        redirectEvent('invalid', 'error');
    }

    if (
        !is_dir($uploadDir) &&
        !mkdir($uploadDir, 0755, true)
    ) {
        redirectEvent('uploadfailed', 'error');
    }

    $poster = null;
    $destination = null;

    // Upload poster if one was selected
    if ($extension !== null) {

        try {
            $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        } catch (Throwable $e) {
            redirectEvent('uploadfailed', 'error');
        }

        $destination = $uploadDir . $filename;

        if (!move_uploaded_file(
            $_FILES['poster']['tmp_name'],
            $destination
        )) {
            redirectEvent('uploadfailed', 'error');
        }

        $poster = $filename;
    }

    // Insert event into database
    $stmt = $conn->prepare(
        "INSERT INTO events
        (event_name, event_date, venue, description, category, status, poster)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        if ($destination && file_exists($destination)) {
            unlink($destination);
        }

        redirectEvent('dbfailed', 'error');
    }

    $stmt->bind_param(
        "sssssss",
        $name,
        $date,
        $venue,
        $description,
        $category,
        $status,
        $poster
    );

    if ($stmt->execute()) {
        $stmt->close();
        redirectEvent('added');
    }

    $stmt->close();

    if ($destination && file_exists($destination)) {
        unlink($destination);
    }

    redirectEvent('dbfailed', 'error');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Event - StudentHub</title>

    <link rel="stylesheet" href="../CSS/events_admin.css">
</head>
<body>

<main class="form-container">
    <h1>Add New Event</h1>

    <form method="POST" enctype="multipart/form-data">

        <label for="event_name">Event Name *</label>
        <input
            id="event_name"
            type="text"
            name="event_name"
            maxlength="150"
            required>

        <label for="event_date">Event Date *</label>
        <input
            id="event_date"
            type="date"
            name="event_date"
            required>

        <label for="venue">Venue *</label>
        <input
            id="venue"
            type="text"
            name="venue"
            maxlength="200"
            required>

        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            rows="4"></textarea>

        <label for="category">Category</label>
        <select id="category" name="category">
            <option value="">Select Category</option>
            <option value="Technical">Technical</option>
            <option value="Cultural">Cultural</option>
            <option value="Sports">Sports</option>
            <option value="Workshop">Workshop</option>
            <option value="Other">Other</option>
        </select>

        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="Open">Open</option>
            <option value="Closed">Closed</option>
        </select>

        <label for="poster">
            Event Poster (optional, max 2 MB)
        </label>

        <input
            id="poster"
            type="file"
            name="poster"
            accept=".jpg,.jpeg,.png,.webp">

        <p class="help-text">
            Allowed formats: JPG, PNG, WebP. Maximum size: 2 MB.
        </p>

        <button type="submit" class="btn btn-primary">
            Save Event
        </button>

        <a href="event_management.php" class="btn btn-secondary">
            Cancel
        </a>
    </form>
</main>

</body>
</html>