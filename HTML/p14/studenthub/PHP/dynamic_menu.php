<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION["role"] ?? "";

$menus = [
    "admin" => [
        ["Dashboard", "admin_dashboard.php"],
        ["Students", "student_management.php"],
        ["Manage Users", "admin_users.php"],
        ["Audit Logs", "audit_logs.php"],
        ["Home", "../pages/index.html"]
    ],

    "student" => [
        ["Home", "../pages/index.html"],
        ["Profile", "../pages/profile.html"],
        ["Events", "../pages/events.html"],
        ["Dashboard", "../pages/dashboard.html"]
    ],

    "staff" => [
        ["Home", "../pages/index.html"],
        ["Students", "student_management.php"],
        ["Events", "../pages/events.html"]
    ]
];

$menuItems = $menus[$role] ?? [];
?>

<nav class="admin-nav" aria-label="Main navigation">

    <?php foreach ($menuItems as $item): ?>

        <a href="<?= htmlspecialchars(
            $item[1],
            ENT_QUOTES,
            "UTF-8"
        ) ?>">
            <?= htmlspecialchars(
                $item[0],
                ENT_QUOTES,
                "UTF-8"
            ) ?>
        </a>

    <?php endforeach; ?>

    <?php if ($role !== ""): ?>
        <a href="logout.php">Logout</a>
    <?php endif; ?>

</nav>