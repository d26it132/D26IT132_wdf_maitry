
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../php/session_guard.php';

require_role('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - StudentHub</title>
</head>
<body>
    <h1>Admin Dashboard</h1>

    <p>
        Welcome,
        <?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?>!
    </p>

    <p>You have logged in successfully as an administrator.</p>

    <a href="../php/logout.php">Logout</a>
</body>
</html>
