
<?php

require __DIR__ . "/db_connect_mysqli.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Please submit the registration form.");
}

// Get form data
$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";

// Server-side validation
if (
    $name === "" ||
    $email === "" ||
    $password === "" ||
    $confirmPassword === ""
) {
    exit("All fields are required.");
}

if (strlen($name) > 100) {
    exit("Name must not exceed 100 characters.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Please enter a valid email address.");
}

if (strlen($email) > 255) {
    exit("Email address is too long.");
}

if (strlen($password) < 6) {
    exit("Password must contain at least 6 characters.");
}

if ($password !== $confirmPassword) {
    exit("Passwords do not match.");
}

// Check whether the email already exists
$check_sql = "SELECT id FROM users WHERE email = ?";

$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    $check_stmt->close();
    $conn->close();
    exit("This email is already registered.");
}

$check_stmt->close();

// Hash the password before saving
$hashed_password = password_hash(
    $password,
    PASSWORD_DEFAULT
);

// Insert the user using a prepared statement
$sql = "INSERT INTO users (name, email, password)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sss",
    $name,
    $email,
    $hashed_password
);

if ($stmt->execute()) {
    echo "<h2>Registration Successful!</h2>";
    echo "<p>User registered securely using MySQLi.</p>";
    echo '<a href="../pages/register_user.html">
          Register Another User</a>';
} else {
    // Error code 1062 means a duplicate-key violation
    if ($stmt->errno == 1062) {
        echo "This email is already registered.";
    } else {
        echo "Registration failed. Please try again.";
    }
}

$stmt->close();
$conn->close();

?>
