
<?php

require __DIR__ . "/db_connect_mysqli.php";

// Fetch registered users
$sql = "SELECT id, name, email, created_at
        FROM users
        ORDER BY id ASC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>View Registered Users</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #243b55;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 25px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #243b55;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        .links {
            text-align: center;
            margin-top: 25px;
        }

        a {
            display: inline-block;
            margin: 8px;
            padding: 10px 15px;
            background: #243b55;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>

    <h2>Registered Users</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Registration Date</th>
            </tr>
        </thead>

        <tbody>

        <?php if ($result && $result->num_rows > 0) { ?>

            <?php while ($row = $result->fetch_assoc()) { ?>

                <tr>
                    <td>
                        <?= htmlspecialchars((string)$row["id"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row["name"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row["email"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row["created_at"]) ?>
                    </td>
                </tr>

            <?php } ?>

        <?php } else { ?>

            <tr>
                <td colspan="4">No registered users found.</td>
            </tr>

        <?php } ?>

        </tbody>
    </table>

    <div class="links">
        <a href="../page/register.html">
            Register Another User
        </a>

        <a href="../page/index.html">
            Home
        </a>
    </div>

</body>
</html>

<?php
$conn->close();
?>
