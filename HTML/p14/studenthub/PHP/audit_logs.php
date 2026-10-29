<?php

require_once __DIR__ . "/auth_check.php";

$result = $conn->query(
    "SELECT
        a.id,
        a.user_id,
        u.name AS user_name,
        a.action,
        a.details,
        a.ip_address,
        a.created_at
     FROM audit_logs a
     LEFT JOIN users u ON a.user_id = u.id
     ORDER BY a.created_at DESC, a.id DESC
     LIMIT 200"
);

function auditEscape($value)
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

    <title>Audit Logs - StudentHub</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet"
          href="../CSS/admin_dashboard.css">
</head>

<body>

<header class="admin-header">
    <h2>Audit Logs</h2>

    <a href="admin_dashboard.php" class="logout-btn">
        Back to Dashboard
    </a>
</header>

<main class="container-fluid p-4">

    <h3 class="mb-3">Administrative Activity History</h3>

    <div class="table-responsive bg-white p-3 rounded">

        <table class="table table-striped table-hover">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>IP Address</th>
                    <th>Date and Time</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($result && $result->num_rows > 0): ?>

                <?php while ($row = $result->fetch_assoc()): ?>

                    <tr>
                        <td><?= (int) $row["id"] ?></td>

                        <td>
                            <?= auditEscape(
                                $row["user_name"] ?? "Deleted User"
                            ) ?>
                        </td>

                        <td><?= auditEscape($row["action"]) ?></td>

                        <td><?= auditEscape($row["details"]) ?></td>

                        <td><?= auditEscape($row["ip_address"]) ?></td>

                        <td><?= auditEscape($row["created_at"]) ?></td>
                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="6" class="text-center">
                        No audit logs found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</main>

</body>
</html>