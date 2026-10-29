
<?php

// Secure session cookie settings for local HTTP development.
ini_set("session.use_strict_mode", "1");
ini_set("session.use_only_cookies", "1");
ini_set("session.use_trans_sid", "0");

session_set_cookie_params([
    "lifetime" => 0,
    "path" => "/",
    "httponly" => true,
    "secure" => !empty($_SERVER["HTTPS"])
        && $_SERVER["HTTPS"] !== "off",
    "samesite" => "Lax"
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function require_role($requiredRole)
{
    // Redirect unauthenticated visitors.
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.html");
        exit;
    }

    // Log out after 15 minutes of inactivity.
    $timeout = 900;

    if (
        !isset($_SESSION["last_activity"]) ||
        time() - $_SESSION["last_activity"] > $timeout
    ) {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                "",
                [
                    "expires" => time() - 42000,
                    "path" => $params["path"],
                    "domain" => $params["domain"],
                    "secure" => $params["secure"],
                    "httponly" => $params["httponly"],
                    "samesite" => $params["samesite"] ?? "Lax"
                ]
            );
        }

        session_destroy();

        header("Location: login.html?timeout=1");
        exit;
    }

    // Update activity time only after the timeout check.
    $_SESSION["last_activity"] = time();

    // Reject users with the wrong role.
    if ($_SESSION["user_role"] !== $requiredRole) {
        if ($_SESSION["user_role"] === "admin") {
            header("Location: admin_dashboard.php");
        } elseif ($_SESSION["user_role"] === "student") {
            header("Location: student_dashboard.php");
        } else {
            $_SESSION = [];
            session_destroy();

            header("Location: login.html?error=1");
        }

        exit;
    }
}

?>
