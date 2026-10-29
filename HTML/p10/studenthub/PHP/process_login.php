
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require __DIR__ . "/db_connect_mysqli.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../page/login.html");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if (
    $email === "" ||
    $password === "" ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    header("Location: ../page/login.html?error=1");
    exit;
}

$sql = "SELECT id, name, email, password, role
        FROM users
        WHERE email = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("LOGIN SELECT ERROR: " . $conn->error);
}

$stmt->bind_param("s", $email);

if (!$stmt->execute()) {
    die("LOGIN EXECUTE ERROR: " . $stmt->error);
}

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (
    !$user ||
    !password_verify($password, $user["password"])
) {
    $stmt->close();
    $conn->close();

    header("Location: ../page/login.html?error=1");
    exit;
}

// Regenerate session ID after successful authentication.
if (!session_regenerate_id(true)) {
    die("Unable to establish a secure session.");
}

// Store authenticated user details.
$_SESSION["user_id"] = $user["id"];
$_SESSION["user_name"] = $user["name"];
$_SESSION["user_email"] = $user["email"];
$_SESSION["user_role"] = $user["role"];
$_SESSION["last_activity"] = time();

// Record last login.

// Redirect based on role.
if ($user["role"] === "admin") {
    header("Location: ../page/admin_dashboard.php");
    exit;
}

if ($user["role"] === "student") {
    header("Location: ../page/student_dashboard.php");
    exit;
}

// Deny any unexpected role.
$_SESSION = [];
session_destroy();
header("Location: ../page/login.html?error=1");
exit;

?>
