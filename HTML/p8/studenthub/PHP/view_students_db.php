
<?php
require __DIR__ . "/db_connect.php";

$sql = "SELECT students.id,
               students.name,
               students.email,
               students.mobile,
               students.year,
               students.gender,
               courses.course_name
        FROM students
        LEFT JOIN courses
        ON students.course_id = courses.course_id
        ORDER BY students.id ASC";

$stmt = $pdo->query($sql);
$students = $stmt->fetchAll();

function h($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registered Students - StudentHub</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f4f6f9;
        }

        h2 {
            text-align: center;
            color: #23395d;
        }

        .top-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            background: #23395d;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
        }

        .table-container {
            overflow-x: auto;
            background: white;
            padding: 15px;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #23395d;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        .update-btn,
        .delete-btn {
            display: inline-block;
            padding: 7px 10px;
            border: none;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .update-btn {
            background: #16803c;
        }

        .delete-btn {
            background: #c62828;
        }

        .delete-form {
            display: inline;
        }

        .empty-message {
            text-align: center;
            padding: 20px;
        }
    </style>
</head>

<body>

    <h2>Registered Students</h2>

    <p>
        <a class="top-link" href="../page/register.html">
            + Register Another Student
        </a>
    </p>

    <?php if (count($students) > 0): ?>

        <div class="table-container">

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Gender</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($students as $student): ?>

                        <tr>
                            <td><?= h($student["id"]) ?></td>
                            <td><?= h($student["name"]) ?></td>
                            <td><?= h($student["email"]) ?></td>
                            <td><?= h($student["mobile"]) ?></td>
                            <td><?= h($student["course_name"] ?? "Not assigned") ?></td>
                            <td><?= h($student["year"]) ?></td>
                            <td><?= h($student["gender"]) ?></td>

                            <td>
                                <a
                                    class="update-btn"
                                    href="update_student_db.php?id=<?= (int)$student["id"] ?>">
                                    Update
                                </a>

                                <form
                                    class="delete-form"
                                    action="delete_student_db.php"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this student?');">

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$student["id"] ?>">

                                    <button
                                        type="submit"
                                        class="delete-btn">
                                        Delete
                                    </button>

                                </form>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>

    <?php else: ?>

        <p class="empty-message">
            No registered students found.
        </p>

    <?php endif; ?>

</body>
</html>
