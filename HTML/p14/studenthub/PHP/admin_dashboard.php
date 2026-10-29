<?php

require_once __DIR__ . "/auth_check.php";
require_once __DIR__ . "/audit_log.php";

// Record dashboard access.
if (!isset($_SESSION["dashboard_logged"])) {
    logAudit(
        $conn,
        $userId,
        "VIEW_DASHBOARD",
        "Admin opened the dashboard"
    );

    $_SESSION["dashboard_logged"] = true;
}

// Get total users.
$userCount = 0;

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM users"
);

if ($result) {
    $userCount = (int) $result->fetch_assoc()["total"];
}

// Get total students.
$studentCount = 0;

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM students"
);

if ($result) {
    $studentCount = (int) $result->fetch_assoc()["total"];
}

// Get total events.
$eventCount = 0;

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM events"
);

if ($result) {
    $eventCount = (int) $result->fetch_assoc()["total"];
}

// Get total event registrations.
$registrationCount = 0;

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM registrations"
);

if ($result) {
    $registrationCount = (int) $result->fetch_assoc()["total"];
}

// Get latest student registrations.
$latestRegistrations = $conn->query(
    "SELECT
        r.id,
        s.name AS student_name,
        s.email,
        e.title AS event_title,
        r.registered_at
     FROM registrations r
     INNER JOIN students s
        ON r.student_id = s.id
     INNER JOIN events e
        ON r.event_id = e.id
     ORDER BY r.registered_at DESC, r.id DESC
     LIMIT 10"
);

// Get latest audit entries.
$auditResult = $conn->query(
    "SELECT
        a.action,
        a.details,
        a.ip_address,
        a.created_at,
        u.name AS user_name
     FROM audit_logs a
     LEFT JOIN users u
        ON a.user_id = u.id
     ORDER BY a.created_at DESC, a.id DESC
     LIMIT 10"
);

function escapeHTML($value)
{
    return htmlspecialchars(
        (string) ($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>StudentHub - Admin Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet"
          href="../CSS/admin_dashboard.css">
</head>

<body>

<header class="admin-header">
    <div>
        <h2>StudentHub Admin</h2>
        <p>Administrative Monitoring Panel</p>
    </div>

    <div class="admin-user">
        Welcome,
        <strong>
            <?= escapeHTML($_SESSION["user_name"] ?? "Admin") ?>
        </strong>

        <span class="role-badge">
            <?= escapeHTML($_SESSION["role"]) ?>
        </span>

        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</header>

<?php require __DIR__ . "/dynamic_menu.php"; ?>

<main class="container-fluid dashboard-container">

    <section class="welcome-section">
        <h1>Admin Dashboard</h1>

        <p>
            Monitor students, users, events, registrations
            and administrative activities.
        </p>
    </section>

    <section class="stats-grid">

        <article class="stat-card users-card">
            <h3>Total Users</h3>
            <p><?= $userCount ?></p>
            <span>Registered user accounts</span>
        </article>

        <article class="stat-card students-card">
            <h3>Total Students</h3>
            <p><?= $studentCount ?></p>
            <span>Students in the database</span>
        </article>

        <article class="stat-card events-card">
            <h3>Total Events</h3>
            <p><?= $eventCount ?></p>
            <span>Events stored in the database</span>
        </article>

        <article class="stat-card registrations-card">
            <h3>Registrations</h3>
            <p><?= $registrationCount ?></p>
            <span>Total event registrations</span>
        </article>

    </section>

    <section class="table-section">

        <h2>Latest Registrations</h2>

        <div class="table-responsive">
            <table class="table table-striped table-hover">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Event</th>
                        <th>Registered At</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (
                    $latestRegistrations &&
                    $latestRegistrations->num_rows > 0
                ): ?>

                    <?php while (
                        $row = $latestRegistrations->fetch_assoc()
                    ): ?>

                        <tr>
                            <td>
                                <?= (int) $row["id"] ?>
                            </td>

                            <td>
                                <?= escapeHTML($row["student_name"]) ?>
                            </td>

                            <td>
                                <?= escapeHTML($row["email"]) ?>
                            </td>

                            <td>
                                <?= escapeHTML($row["event_title"]) ?>
                            </td>

                            <td>
                                <?= escapeHTML($row["registered_at"]) ?>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="text-center">
                            No registrations found.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>
        </div>

    </section>

    <section class="table-section">

        <h2>Recent Audit Logs</h2>

        <div class="table-responsive">
            <table class="table table-striped table-hover">

                <thead>
                    <tr>
                        <th>Admin/User</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>IP Address</th>
                        <th>Date and Time</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (
                    $auditResult &&
                    $auditResult->num_rows > 0
                ): ?>

                    <?php while (
                        $log = $auditResult->fetch_assoc()
                    ): ?>

                        <tr>
                            <td>
                                <?= escapeHTML(
                                    $log["user_name"] ?? "Deleted User"
                                ) ?>
                            </td>

                            <td>
                                <?= escapeHTML($log["action"]) ?>
                            </td>

                            <td>
                                <?= escapeHTML($log["details"]) ?>
                            </td>

                            <td>
                                <?= escapeHTML($log["ip_address"]) ?>
                            </td>

                            <td>
                                <?= escapeHTML($log["created_at"]) ?>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="text-center">
                            No audit records found.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>
        </div>

    </section>

</main>

<footer class="admin-footer">
    <p>&copy; 2026 StudentHub | Admin Dashboard</p>
</footer>

</body>
</html>