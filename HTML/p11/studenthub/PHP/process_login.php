
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require __DIR__ . "/db_connect_mysqli.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/login.html");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if (
    $email === "" ||
    $password === "" ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    header("Location: ../pages/login.html?error=1");
    exit;
}

$sql = "SELECT id, name, email, password, role, status
        FROM users
        WHERE email = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Prepare Error: " . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (
    !$user ||
    !password_verify($password, $user["password"])
) {
    $stmt->close();
    $conn->close();

    header("Location: ../pages/login.html?error=1");
    exit;
}

// Check whether the account is active.
if ($user["status"] !== "active") {
    $stmt->close();
    $conn->close();

    header("Location: ../pages/login.html?error=inactive");
    exit;
}

// Regenerate session ID after login.
if (!session_regenerate_id(true)) {
    die("Unable to establish a secure session.");
}

// Store user details in the session.
$_SESSION["user_id"] = $user["id"];
$_SESSION["user_name"] = $user["name"];
$_SESSION["user_email"] = $user["email"];
$_SESSION["user_role"] = $user["role"];
$_SESSION["last_activity"] = time();

$stmt->close();

// Update last login time.
$update = $conn->prepare(
    "UPDATE users SET last_login = NOW() WHERE id = ?"
);

if (!$update) {
    die("Last Login Update Error: " . $conn->error);
}

$update->bind_param("i", $user["id"]);
$update->execute();

$update->close();
$conn->close();

// Redirect according to role.
if ($user["role"] === "admin") {
    header("Location: student_management.php");
    exit;
}

if ($user["role"] === "student") {
    header("Location: ../page/student_dashboard.php");
    exit;
}

// Reject unexpected roles.
$_SESSION = [];
session_destroy();

header("Location: ../page/login.html?error=1");
exit;

?>
