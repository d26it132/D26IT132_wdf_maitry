
<?php
require __DIR__ . "/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: view_students_db.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid student ID.");
}

try {
    $stmt = $pdo->prepare(
        "DELETE FROM students WHERE id = :id"
    );

    $stmt->execute([":id" => $id]);

    if ($stmt->rowCount() > 0) {
        header("Location: view_students_db.php");
        exit;
    }

    echo "Student not found or already deleted.";
    echo '<p><a href="view_students_db.php">Back to Students</a></p>';

} catch (PDOException $e) {
    if ($e->getCode() === "23000") {
        exit("This student cannot be deleted because another database record depends on it.");
    }

    exit("Unable to delete student. Please check the database.");
}
?>
