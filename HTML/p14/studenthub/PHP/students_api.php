<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/db_connect_mysqli.php";

function sendResponse($status, $data)
{
    http_response_code($status);
    echo json_encode($data);
    exit;
}

if (!isset($conn) || !($conn instanceof mysqli)) {
    sendResponse(500, [
        "success" => false,
        "message" => "MySQLi connection is not available."
    ]);
}

$method = $_SERVER["REQUEST_METHOD"];

// READ AND SEARCH STUDENTS
if ($method === "GET") {

    $search = trim($_GET["search"] ?? "");

    if ($search !== "") {

        $sql = "SELECT id, name, email, mobile, course_id, year, gender
                FROM students
                WHERE name LIKE ? OR email LIKE ?
                ORDER BY id ASC";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            sendResponse(500, [
                "success" => false,
                "message" => "Failed to prepare search query."
            ]);
        }

        $searchTerm = "%" . $search . "%";
        $stmt->bind_param("ss", $searchTerm, $searchTerm);

    } else {

        $sql = "SELECT id, name, email, mobile, course_id, year, gender
                FROM students
                ORDER BY id ASC";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            sendResponse(500, [
                "success" => false,
                "message" => "Failed to prepare query."
            ]);
        }
    }

    if (!$stmt->execute()) {
        sendResponse(500, [
            "success" => false,
            "message" => "Failed to fetch students."
        ]);
    }

    $result = $stmt->get_result();
    $students = [];

    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    sendResponse(200, [
        "success" => true,
        "data" => $students
    ]);
}


// READ JSON DATA FOR POST, PUT AND DELETE
$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input)) {
    sendResponse(400, [
        "success" => false,
        "message" => "Invalid JSON data."
    ]);
}


// ADD STUDENT
if ($method === "POST") {

    $name = trim($input["name"] ?? "");
    $email = trim($input["email"] ?? "");
    $mobile = trim($input["mobile"] ?? "");
    $courseId = trim((string)($input["course_id"] ?? ""));
    $year = trim((string)($input["year"] ?? ""));
    $gender = trim($input["gender"] ?? "");

    if (
        $name === "" ||
        $email === "" ||
        $mobile === "" ||
        $courseId === "" ||
        $year === "" ||
        $gender === ""
    ) {
        sendResponse(400, [
            "success" => false,
            "message" => "Please fill in all fields."
        ]);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse(400, [
            "success" => false,
            "message" => "Please enter a valid email address."
        ]);
    }

    if (!ctype_digit($courseId) || (int)$courseId < 1) {
        sendResponse(400, [
            "success" => false,
            "message" => "Course ID must be a positive number."
        ]);
    }

    if (!ctype_digit($year) || (int)$year < 1) {
        sendResponse(400, [
            "success" => false,
            "message" => "Year must be a positive number."
        ]);
    }

    if (!preg_match('/^[0-9]{10,15}$/', $mobile)) {
        sendResponse(400, [
            "success" => false,
            "message" => "Mobile must contain 10 to 15 digits."
        ]);
    }

    if (!in_array($gender, ["Male", "Female", "Other"], true)) {
        sendResponse(400, [
            "success" => false,
            "message" => "Please select a valid gender."
        ]);
    }

    // Check for duplicate email
    $check = $conn->prepare(
        "SELECT id FROM students WHERE email = ?"
    );

    $check->bind_param("s", $email);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows > 0) {
        sendResponse(409, [
            "success" => false,
            "message" => "This email is already registered."
        ]);
    }

    $sql = "INSERT INTO students
            (name, email, mobile, course_id, year, gender)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        sendResponse(500, [
            "success" => false,
            "message" => "Failed to prepare insert query."
        ]);
    }

    $courseId = (int)$courseId;
    $year = (int)$year;

    $stmt->bind_param(
        "sssiis",
        $name,
        $email,
        $mobile,
        $courseId,
        $year,
        $gender
    );

    if ($stmt->execute()) {
        sendResponse(201, [
            "success" => true,
            "message" => "Student added successfully.",
            "id" => $conn->insert_id
        ]);
    }

    if ($stmt->errno === 1062) {
        sendResponse(409, [
            "success" => false,
            "message" => "This email is already registered."
        ]);
    }

    sendResponse(500, [
        "success" => false,
        "message" => "Failed to add student."
    ]);
}


// UPDATE STUDENT
if ($method === "PUT") {

    $id = filter_var(
        $input["id"] ?? null,
        FILTER_VALIDATE_INT
    );

    if (!$id || $id < 1) {
        sendResponse(400, [
            "success" => false,
            "message" => "A valid student ID is required."
        ]);
    }

    $name = trim($input["name"] ?? "");
    $email = trim($input["email"] ?? "");
    $mobile = trim($input["mobile"] ?? "");
    $courseId = trim((string)($input["course_id"] ?? ""));
    $year = trim((string)($input["year"] ?? ""));
    $gender = trim($input["gender"] ?? "");

    if (
        $name === "" ||
        $email === "" ||
        $mobile === "" ||
        $courseId === "" ||
        $year === "" ||
        $gender === ""
    ) {
        sendResponse(400, [
            "success" => false,
            "message" => "Please fill in all fields."
        ]);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse(400, [
            "success" => false,
            "message" => "Please enter a valid email address."
        ]);
    }

    if (!ctype_digit($courseId) || (int)$courseId < 1) {
        sendResponse(400, [
            "success" => false,
            "message" => "Course ID must be a positive number."
        ]);
    }

    if (!ctype_digit($year) || (int)$year < 1) {
        sendResponse(400, [
            "success" => false,
            "message" => "Year must be a positive number."
        ]);
    }

    if (!preg_match('/^[0-9]{10,15}$/', $mobile)) {
        sendResponse(400, [
            "success" => false,
            "message" => "Mobile must contain 10 to 15 digits."
        ]);
    }

    if (!in_array($gender, ["Male", "Female", "Other"], true)) {
        sendResponse(400, [
            "success" => false,
            "message" => "Please select a valid gender."
        ]);
    }

    // Check whether the student exists
    $check = $conn->prepare(
        "SELECT id FROM students WHERE id = ?"
    );

    $check->bind_param("i", $id);
    $check->execute();

    if ($check->get_result()->num_rows === 0) {
        sendResponse(404, [
            "success" => false,
            "message" => "Student not found."
        ]);
    }

    // Check duplicate email belonging to another student
    $checkEmail = $conn->prepare(
        "SELECT id FROM students WHERE email = ? AND id != ?"
    );

    $checkEmail->bind_param("si", $email, $id);
    $checkEmail->execute();

    if ($checkEmail->get_result()->num_rows > 0) {
        sendResponse(409, [
            "success" => false,
            "message" => "This email belongs to another student."
        ]);
    }

    $sql = "UPDATE students
            SET name = ?, email = ?, mobile = ?,
                course_id = ?, year = ?, gender = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        sendResponse(500, [
            "success" => false,
            "message" => "Failed to prepare update query."
        ]);
    }

    $courseId = (int)$courseId;
    $year = (int)$year;

    $stmt->bind_param(
        "sssiisi",
        $name,
        $email,
        $mobile,
        $courseId,
        $year,
        $gender,
        $id
    );

    if ($stmt->execute()) {
        sendResponse(200, [
            "success" => true,
            "message" => "Student updated successfully."
        ]);
    }

    sendResponse(500, [
        "success" => false,
        "message" => "Failed to update student."
    ]);
}


// DELETE STUDENT
if ($method === "DELETE") {

    $id = filter_var(
        $input["id"] ?? null,
        FILTER_VALIDATE_INT
    );

    if (!$id || $id < 1) {
        sendResponse(400, [
            "success" => false,
            "message" => "A valid student ID is required."
        ]);
    }

    $stmt = $conn->prepare(
        "DELETE FROM students WHERE id = ?"
    );

    if (!$stmt) {
        sendResponse(500, [
            "success" => false,
            "message" => "Failed to prepare delete query."
        ]);
    }

    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        sendResponse(500, [
            "success" => false,
            "message" => "Failed to delete student."
        ]);
    }

    if ($stmt->affected_rows === 0) {
        sendResponse(404, [
            "success" => false,
            "message" => "Student not found."
        ]);
    }

    sendResponse(200, [
        "success" => true,
        "message" => "Student deleted successfully."
    ]);
}


// UNSUPPORTED METHOD
header("Allow: GET, POST, PUT, DELETE");

sendResponse(405, [
    "success" => false,
    "message" => "Method not allowed."
]);

?>
