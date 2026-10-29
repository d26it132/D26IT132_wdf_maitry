<?php

session_start();

require_once __DIR__ . "/db_connect_mysqli.php";
require_once __DIR__ . "/audit_log.php";

if (isset($_SESSION["user_id"])) {

    logAudit(
        $conn,
        (int) $_SESSION["user_id"],
        "LOGOUT",
        "User logged out"
    );
}

$_SESSION = [];

session_destroy();

header("Location: ../pages/login.php");
exit;
?>