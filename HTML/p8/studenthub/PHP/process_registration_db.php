
<?php

require __DIR__ . "/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/register.html");
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$course_id = $_POST["course_id"] ?? "";
$year = $_POST["year"] ?? "";
$gender = strtolower(trim($_POST["gender"] ?? ""));
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";
$terms = isset($_POST["terms"]);

// Validate the form data
if (
    $name === "" ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    !preg_match('/^[0-9]{10}$/', $mobile) ||
    !ctype_digit((string)$course_id) ||
    !in_array((int)$course_id, [1, 2, 3], true) ||
    !ctype_digit((string)$year) ||
    (int)$year < 1 ||
    (int)$year > 4 ||
    !in_array($gender, ["male", "female", "other"], true) ||
    !$terms
) {
    exit("Invalid input. Please go back and check the form.");
}

try {
    $sql = "INSERT INTO students
            (name, email, mobile, course_id, year, gender)
            VALUES
            (:name, :email, :mobile, :course_id, :year, :gender)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":name" => $name,
        ":email" => $email,
        ":mobile" => $mobile,
        ":course_id" => (int)$course_id,
        ":year" => (int)$year,
        ":gender" => ucfirst($gender)
    ]);

    echo "<h2>Registration Successful!</h2>";
    echo "<p>Student data has been saved to MySQL.</p>";
    echo "<br>";
    echo '<a href="view_students_db.php">View Registered Students</a>';

} catch (PDOException $e) {
    if ($e->getCode() === "23000") {
        exit("Unable to register the student. Check the email, selected course, and database constraints.");
    }

    exit("Unable to save student data. Check your database table and column names.");
}
?>
