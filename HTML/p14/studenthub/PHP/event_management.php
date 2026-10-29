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

// Fetch events in ascending ID order
$sql = "SELECT * FROM events ORDER BY id ASC";
$result = $conn->query($sql);

if (!$result) {
    die("Unable to retrieve events.");
}

$message = $_GET['message'] ?? '';
$type = $_GET['type'] ?? 'success';

$messages = [
    'added'       => 'Event added successfully.',
    'updated'     => 'Event updated successfully.',
    'deleted'     => 'Event deleted successfully.',
    'invalid'     => 'Invalid event details or poster file.',
    'failed'      => 'Operation failed. Please try again.',
    'notfound'    => 'Event not found.',
    'uploadfailed'=> 'Poster upload failed.',
    'dbfailed'    => 'Database operation failed.'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StudentHub - Event Management</title>

    <link rel="stylesheet" href="../CSS/events_admin.css">
</head>
<body>

<header class="admin-header">
    <div>
        <h1>StudentHub</h1>
        <p>Event Management Dashboard</p>
    </div>

    <a href="../php/student_management.php" class="home-btn">Student Management</a>
    <a class="home-btn" href="logout.php">Logout</a>
</header>

<main class="container">

    <div class="page-heading">
        <div>
            <h2>Manage Events</h2>
            <p>Add, view, edit and delete events.</p>
        </div>

        <a href="add_event.php" class="btn btn-primary">
            + Add New Event
        </a>
    </div>

    <?php if (isset($messages[$message])): ?>
        <div class="alert <?= $type === 'error'
            ? 'alert-error'
            : 'alert-success' ?>">
            <?= htmlspecialchars($messages[$message]) ?>
        </div>
    <?php endif; ?>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Poster</th>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Venue</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($result->num_rows > 0): ?>

                <?php while ($event = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= (int) $event['id'] ?></td>

                        <td>
                            <?php if (!empty($event['poster'])): ?>
                                <img
                                    class="poster-preview"
                                    src="../uploads/event_posters/<?= rawurlencode(basename($event['poster'])) ?>"
                                    alt="Event poster">
                            <?php else: ?>
                                <span>No poster</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($event['event_name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($event['event_date']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($event['venue']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($event['category'] ?? '') ?>
                        </td>

                        <td>
                            <span class="status <?= $event['status'] === 'Open'
                                ? 'open'
                                : 'closed' ?>">
                                <?= htmlspecialchars($event['status']) ?>
                            </span>
                        </td>

                        <td class="actions">
                            <a
                                class="btn btn-edit"
                                href="edit_event.php?id=<?= (int) $event['id'] ?>">
                                Edit
                            </a>

                            <form
                                action="delete_event.php"
                                method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this event?');">

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int) $event['id'] ?>">

                                <button type="submit" class="btn btn-delete">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>

            <?php else: ?>
                <tr>
                    <td colspan="8" class="empty">
                        No events found. Click Add New Event to create one.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

</body>
</html>