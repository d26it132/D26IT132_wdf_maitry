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

function redirectEdit($message, $type = 'success')
{
    header(
        "Location: event_management.php?message=" .
        urlencode($message) . "&type=" . urlencode($type)
    );
    exit;
}

function checkEditPoster($file)
{
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (
        $file['error'] !== UPLOAD_ERR_OK ||
        $file['size'] <= 0 ||
        $file['size'] > 2 * 1024 * 1024
    ) {
        return false;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if (
        !isset($allowed[$mime]) ||
        @getimagesize($file['tmp_name']) === false
    ) {
        return false;
    }

    return $allowed[$mime];
}

// Validate event ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    redirectEdit('notfound', 'error');
}

// Fetch existing event
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$event = $result->fetch_assoc();

$stmt->close();

if (!$event) {
    redirectEdit('notfound', 'error');
}

// Process update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['event_name'] ?? '');
    $date = trim($_POST['event_date'] ?? '');
    $venue = trim($_POST['venue'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $status = $_POST['status'] ?? '';

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
        redirectEdit('invalid', 'error');
    }

    $extension = checkEditPoster($_FILES['poster'] ?? null);

    if ($extension === false) {
        redirectEdit('invalid', 'error');
    }

    $oldPoster = $event['poster'];
    $newPoster = $oldPoster;
    $destination = null;

    // Upload a replacement poster if selected
    if ($extension !== null) {

        if (
            !is_dir($uploadDir) &&
            !mkdir($uploadDir, 0755, true)
        ) {
            redirectEdit('uploadfailed', 'error');
        }

        try {
            $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        } catch (Throwable $e) {
            redirectEdit('uploadfailed', 'error');
        }

        $destination = $uploadDir . $filename;

        if (!move_uploaded_file(
            $_FILES['poster']['tmp_name'],
            $destination
        )) {
            redirectEdit('uploadfailed', 'error');
        }

        $newPoster = $filename;
    }

    // Update event record
    $stmt = $conn->prepare(
        "UPDATE events
         SET event_name = ?,
             event_date = ?,
             venue = ?,
             description = ?,
             category = ?,
             status = ?,
             poster = ?
         WHERE id = ?"
    );

    if (!$stmt) {
        if ($destination && file_exists($destination)) {
            unlink($destination);
        }

        redirectEdit('dbfailed', 'error');
    }

    $stmt->bind_param(
        "sssssssi",
        $name,
        $date,
        $venue,
        $description,
        $category,
        $status,
        $newPoster,
        $id
    );

    $success = $stmt->execute();
    $stmt->close();

    if (!$success) {
        if ($destination && file_exists($destination)) {
            unlink($destination);
        }

        redirectEdit('dbfailed', 'error');
    }

    // Delete old poster only after successful update
    if (
        $destination &&
        !empty($oldPoster) &&
        basename($oldPoster) === $oldPoster
    ) {
        $oldPath = $uploadDir . $oldPoster;

        if (is_file($oldPath)) {
            unlink($oldPath);
        }
    }

    redirectEdit('updated');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Event - StudentHub</title>

    <link rel="stylesheet" href="../CSS/events_admin.css">
</head>
<body>

<main class="form-container">
    <h1>Edit Event</h1>

    <form method="POST" enctype="multipart/form-data">

        <label for="event_name">Event Name *</label>
        <input
            id="event_name"
            type="text"
            name="event_name"
            maxlength="150"
            value="<?= htmlspecialchars($event['event_name']) ?>"
            required>

        <label for="event_date">Event Date *</label>
        <input
            id="event_date"
            type="date"
            name="event_date"
            value="<?= htmlspecialchars($event['event_date']) ?>"
            required>

        <label for="venue">Venue *</label>
        <input
            id="venue"
            type="text"
            name="venue"
            maxlength="200"
            value="<?= htmlspecialchars($event['venue']) ?>"
            required>

        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            rows="4"><?= htmlspecialchars($event['description'] ?? '') ?></textarea>

        <label for="category">Category</label>

        <?php
        $categories = [
            'Technical',
            'Cultural',
            'Sports',
            'Workshop',
            'Other'
        ];
        ?>

        <select id="category" name="category">
            <option value="">Select Category</option>

            <?php foreach ($categories as $categoryOption): ?>
                <option
                    value="<?= htmlspecialchars($categoryOption) ?>"
                    <?= ($event['category'] === $categoryOption)
                        ? 'selected'
                        : '' ?>>
                    <?= htmlspecialchars($categoryOption) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="status">Status</label>

        <select id="status" name="status">
            <option
                value="Open"
                <?= $event['status'] === 'Open' ? 'selected' : '' ?>>
                Open
            </option>

            <option
                value="Closed"
                <?= $event['status'] === 'Closed' ? 'selected' : '' ?>>
                Closed
            </option>
        </select>

        <label>Current Poster</label>

        <?php if (!empty($event['poster'])): ?>
            <p>
                <img
                    class="poster-preview large"
                    src="../uploads/event_posters/<?= rawurlencode(basename($event['poster'])) ?>"
                    alt="Current event poster">
            </p>
        <?php else: ?>
            <p>No poster uploaded.</p>
        <?php endif; ?>

        <label for="poster">
            Replace Poster (optional, max 2 MB)
        </label>

        <input
            id="poster"
            type="file"
            name="poster"
            accept=".jpg,.jpeg,.png,.webp">

        <p class="help-text">
            Leave this field empty to keep the existing poster.
            Allowed formats: JPG, PNG and WebP.
        </p>

        <button type="submit" class="btn btn-primary">
            Update Event
        </button>

        <a href="event_management.php" class="btn btn-secondary">
            Cancel
        </a>
    </form>
</main>

</body>
</html>