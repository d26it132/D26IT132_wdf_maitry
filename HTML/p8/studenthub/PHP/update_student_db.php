
<?php
require __DIR__ . "/db_connect.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid student ID.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $course_id = $_POST["course_id"] ?? "";
    $year = $_POST["year"] ?? "";
    $gender = strtolower(trim($_POST["gender"] ?? ""));

    if (
        $name === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !preg_match('/^[0-9]{10}$/', $mobile) ||
        !ctype_digit((string)$course_id) ||
        !in_array((int)$course_id, [1, 2, 3], true) ||
        !ctype_digit((string)$year) ||
        (int)$year < 1 ||
        (int)$year > 4 ||
        !in_array($gender, ["male", "female", "other"], true)
    ) {
        $error = "Please enter valid student details.";
    } else {
        try {
            $sql = "UPDATE students
                    SET name = :name,
                        email = :email,
                        mobile = :mobile,
                        course_id = :course_id,
                        year = :year,
                        gender = :gender
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":name" => $name,
                ":email" => $email,
                ":mobile" => $mobile,
                ":course_id" => (int)$course_id,
                ":year" => (int)$year,
                ":gender" => ucfirst($gender),
                ":id" => $id
            ]);

            header("Location: view_students_db.php");
            exit;

        } catch (PDOException $e) {
            $error = "Unable to update student. Check the email and database constraints.";
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
$stmt->execute([":id" => $id]);
$student = $stmt->fetch();

if (!$student) {
    exit("Student not found.");
}

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student - StudentHub</title>
</head>
<body>

<h2>Update Student Details</h2>

<?php if ($error !== ""): ?>
    <p style="color:red;"><?= h($error) ?></p>
<?php endif; ?>

<form method="POST"
      action="update_student_db.php?id=<?= (int)$id ?>">

    <label for="name">Full Name:</label><br>
    <input type="text" id="name" name="name"
           value="<?= h($student["name"]) ?>" required>
    <br><br>

    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email"
           value="<?= h($student["email"]) ?>" required>
    <br><br>

    <label for="mobile">Mobile:</label><br>
    <input type="tel" id="mobile" name="mobile"
           pattern="[0-9]{10}"
           value="<?= h($student["mobile"]) ?>" required>
    <br><br>

    <label for="course_id">Course:</label><br>
    <select id="course_id" name="course_id" required>
        <option value="">Select Course</option>
        <?php
        $courses = $pdo->query(
            "SELECT course_id, course_name FROM courses ORDER BY course_id"
        )->fetchAll();

        foreach ($courses as $course):
        ?>
            <option value="<?= (int)$course["course_id"] ?>"
                <?= (int)$student["course_id"] === (int)$course["course_id"]
                    ? "selected" : "" ?>>
                <?= h($course["course_name"]) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label for="year">Year:</label><br>
    <select id="year" name="year" required>
        <?php for ($i = 1; $i <= 4; $i++): ?>
            <option value="<?= $i ?>"
                <?= (int)$student["year"] === $i ? "selected" : "" ?>>
                <?= ["", "First Year", "Second Year",
                     "Third Year", "Fourth Year"][$i] ?>
            </option>
        <?php endfor; ?>
    </select>
    <br><br>

    <label for="gender">Gender:</label><br>
    <select id="gender" name="gender" required>
        <?php foreach (["male", "female", "other"] as $g): ?>
            <option value="<?= $g ?>"
                <?= strtolower($student["gender"]) === $g
                    ? "selected" : "" ?>>
                <?= ucfirst($g) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <button type="submit">Save Changes</button>
    <a href="view_students_db.php">Cancel</a>

</form>

</body>
</html>
