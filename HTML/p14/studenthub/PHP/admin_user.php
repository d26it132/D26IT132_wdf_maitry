<?php

require_once __DIR__ . "/auth_check.php";
require_once __DIR__ . "/audit_log.php";

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST["csrf_token"] ?? "";

    if (!hash_equals($_SESSION["csrf_token"], $token)) {
        http_response_code(403);
        exit("Invalid request token.");
    }

    $targetId = filter_input(
        INPUT_POST,
        "user_id",
        FILTER_VALIDATE_INT
    );

    $newStatus = $_POST["status"] ?? "";

    if (
        !$targetId ||
        !in_array($newStatus, ["active", "inactive"], true)
    ) {
        $message = "Invalid user or status.";
    } elseif ($targetId === $userId && $newStatus === "inactive") {
        $message = "You cannot deactivate your own account.";
    } else {

        $stmt = $conn->prepare(
            "UPDATE users SET status = ? WHERE id = ?"
        );

        $stmt->bind_param("si", $newStatus, $targetId);

        if ($stmt->execute() && $stmt->affected_rows >= 0) {

            logAudit(
                $conn,
                $userId,
                "USER_STATUS_CHANGED",
                "User ID " . $targetId .
                " status set to " . $newStatus
            );

            $message = "User status updated successfully.";

        } else {
            $message = "Unable to update user status.";
        }

        $stmt->close();
    }
}

$result = $conn->query(
    "SELECT id, name, email, role, status, created_at
     FROM users
     ORDER BY id ASC"
);

function userEscape($value)
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

    <title>Manage Users - StudentHub</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet"
          href="../CSS/admin_dashboard.css">
</head>

<body>

<header class="admin-header">
    <h2>Manage Users</h2>
    <a href="admin_dashboard.php" class="logout-btn">
        Back to Dashboard
    </a>
</header>

<main class="container py-4">

    <?php if ($message !== ""): ?>
        <div class="alert alert-info">
            <?= userEscape($message) ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive bg-white p-3 rounded">

        <table class="table table-striped">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php while ($row = $result->fetch_assoc()): ?>

                <tr>
                    <td><?= (int) $row["id"] ?></td>

                    <td><?= userEscape($row["name"]) ?></td>

                    <td><?= userEscape($row["email"]) ?></td>

                    <td><?= userEscape($row["role"]) ?></td>

                    <td><?= userEscape($row["status"]) ?></td>

                    <td>
                        <form method="POST"
                              onsubmit="return confirm('Change this user status?');">

                            <input type="hidden"
                                   name="csrf_token"
                                   value="<?= userEscape($_SESSION["csrf_token"]) ?>">

                            <input type="hidden"
                                   name="user_id"
                                   value="<?= (int) $row["id"] ?>">

                            <?php if ($row["status"] === "active"): ?>

                                <input type="hidden"
                                       name="status"
                                       value="inactive">

                                <button type="submit"
                                        class="btn btn-danger btn-sm">
                                    Deactivate
                                </button>

                            <?php else: ?>

                                <input type="hidden"
                                       name="status"
                                       value="active">

                                <button type="submit"
                                        class="btn btn-success btn-sm">
                                    Activate
                                </button>

                            <?php endif; ?>

                        </form>
                    </td>
                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</main>

</body>
</html>