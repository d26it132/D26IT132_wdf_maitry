<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StudentHub - AJAX Student Management</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f6fb;
            color: #263238;
        }

        header {
            background: #12345a;
            color: white;
            padding: 20px 5%;
        }

        header h1 {
            margin: 0;
            font-size: 25px;
        }

        header p {
            margin-bottom: 0;
        }

        .container {
            width: 94%;
            max-width: 1250px;
            margin: 25px auto;
        }

        .panel {
            background: white;
            padding: 22px;
            margin-bottom: 22px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
        }

        h2 {
            margin-top: 0;
            color: #12345a;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .field label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }

        .field input,
        .field select,
        #search {
            width: 100%;
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            font-size: 15px;
        }

        .buttons {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        button {
            padding: 10px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .primary {
            background: #12345a;
            color: white;
        }

        .secondary {
            background: #64748b;
            color: white;
        }

        .edit {
            background: #f0ad4e;
            color: #222;
        }

        .delete {
            background: #dc3545;
            color: white;
        }

        .search-box {
            max-width: 450px;
            margin-bottom: 15px;
        }

        .message {
            display: none;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .success {
            display: block;
            background: #d1e7dd;
            color: #0f5132;
        }

        .error {
            display: block;
            background: #f8d7da;
            color: #842029;
        }

        .loading {
            margin-bottom: 12px;
            color: #475569;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        th, td {
            border: 1px solid #dbe2ea;
            padding: 11px;
            text-align: left;
        }

        th {
            background: #12345a;
            color: white;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .action-buttons {
            display: flex;
            gap: 7px;
        }

        footer {
            text-align: center;
            padding: 18px;
            color: #64748b;
        }

        @media (max-width: 750px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .panel {
                padding: 15px;
            }

            header h1 {
                font-size: 21px;
            }
        }


        .ajax-link {
            display: inline-block;
            background: #ffffff;
            color: #174ea6;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }

        .ajax-link:hover {
                background: #1d4f82;
        }

        .dashboard-header {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    flex-wrap: wrap;
    column-gap: 8px;
    row-gap: 8px;

    padding: 24px 35px;
    margin-bottom: 25px;

    background: linear-gradient(135deg, #123b72, #2563a6);
    color: #ffffff;
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(18, 59, 114, 0.18);
}

.dashboard-header > div {
    margin-right: auto;
}

.dashboard-header h1 {
    margin: 0 0 6px;
    font-size: 30px;
    font-weight: 700;
    letter-spacing: 0.5px;
    color: #ffffff;
}

.dashboard-header p {
    margin: 0;
    font-size: 15px;
    color: #e0ecff;
}

.dashboard-header .dashboard-logout {
    display: inline-block;
    margin: 0;
    padding: 8px 12px;

    background: #ffffff;
    color: #174ea6;
    text-decoration: none;
    font-weight: 600;
    border-radius: 8px;

    transition: background 0.2s ease, transform 0.2s ease;
}

.dashboard-header .dashboard-logout:hover {
    background: #e0ecff;
    transform: translateY(-2px);
}

.dashboard-header .dashboard-logout:focus-visible {
    outline: 3px solid #facc15;
    outline-offset: 3px;
}


    </style>
</head>

<body>

<header class="dashboard-header">
    <div>
        <h1>StudentHub - Student Management</h1>
        <p>PHP JSON API and Fetch API CRUD without page reload</p>
    </div>

    <a href="student_management.php" class="ajax-link">Student Management</a>
</header>

<main class="container">

    <section class="panel">
        <h2 id="formHeading">Add Student</h2>

        <div id="message" class="message" role="status"
             aria-live="polite"></div>

        <form id="studentForm">

            <input type="hidden" id="studentId">

            <div class="form-grid">

                <div class="field">
                    <label for="name">Student Name</label>
                    <input type="text" id="name" maxlength="100" required>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" maxlength="150" required>
                </div>

                <div class="field">
                    <label for="mobile">Mobile Number</label>
                    <input type="tel" id="mobile"
                           pattern="[0-9]{10,15}"
                           maxlength="15"
                           minlength="10"
                           required>
                </div>

                <div class="field">
                    <label for="course_id">Course ID</label>
                    <input type="number" id="course_id"
                           min="1" step="1" required>
                </div>

                <div class="field">
                    <label for="year">Year</label>
                    <input type="number" id="year"
                           min="1" step="1" required>
                </div>

                <div class="field">
                    <label for="gender">Gender</label>
                    <select id="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

            </div>

            <div class="buttons">
                <button type="submit" class="primary" id="submitButton">
                    Add Student
                </button>

                <button type="button" class="secondary"
                        id="cancelButton" hidden>
                    Cancel Edit
                </button>

                <button type="reset" class="secondary">
                    Clear Form
                </button>
            </div>

        </form>
    </section>


    <section class="panel">

        <h2>Student Records</h2>

        <div class="search-box">
            <label for="search">Search by name or email</label>
            <input type="search" id="search"
                   placeholder="Type a name or email...">
        </div>

        <div id="loading" class="loading" hidden>
            Loading student records...
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Course ID</th>
                        <th>Year</th>
                        <th>Gender</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody id="studentTable">
                    <tr>
                        <td colspan="8">Loading records...</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </section>

</main>

<footer>
    &copy; 2026 StudentHub
</footer>


<script>
    const API_URL = "students_api.php";

    const form = document.getElementById("studentForm");
    const studentId = document.getElementById("studentId");
    const nameInput = document.getElementById("name");
    const emailInput = document.getElementById("email");
    const mobileInput = document.getElementById("mobile");
    const courseInput = document.getElementById("course_id");
    const yearInput = document.getElementById("year");
    const genderInput = document.getElementById("gender");

    const tableBody = document.getElementById("studentTable");
    const messageBox = document.getElementById("message");
    const loading = document.getElementById("loading");
    const searchInput = document.getElementById("search");

    const submitButton = document.getElementById("submitButton");
    const cancelButton = document.getElementById("cancelButton");
    const formHeading = document.getElementById("formHeading");

    let searchTimer;


    // Display success or error messages
    function showMessage(message, type) {
        messageBox.textContent = message;
        messageBox.className = "message " + type;
    }


    function clearMessage() {
        messageBox.textContent = "";
        messageBox.className = "message";
    }


    // Fetch JSON and handle HTTP errors
    async function apiRequest(url, options = {}) {
        const response = await fetch(url, options);

        let result;

        try {
            result = await response.json();
        } catch (error) {
            throw new Error("The server did not return valid JSON.");
        }

        if (!response.ok || result.success === false) {
            throw new Error(result.message || "Request failed.");
        }

        return result;
    }


    // Load and display students
    async function loadStudents() {
        loading.hidden = false;

        try {
            const search = searchInput.value.trim();

            const url = API_URL +
                "?search=" + encodeURIComponent(search);

            const result = await apiRequest(url);

            tableBody.replaceChildren();

            if (result.data.length === 0) {
                const row = document.createElement("tr");
                const cell = document.createElement("td");

                cell.colSpan = 8;
                cell.textContent = "No student records found.";

                row.appendChild(cell);
                tableBody.appendChild(row);

                return;
            }

            result.data.forEach(function(student) {
                const row = document.createElement("tr");

                [
                    student.id,
                    student.name,
                    student.email,
                    student.mobile,
                    student.course_id ?? "",
                    student.year ?? "",
                    student.gender ?? ""
                ].forEach(function(value) {
                    const cell = document.createElement("td");
                    cell.textContent = value ?? "";
                    row.appendChild(cell);
                });

                const actionCell = document.createElement("td");
                const actionButtons = document.createElement("div");

                actionButtons.className = "action-buttons";

                const editButton = document.createElement("button");
                editButton.type = "button";
                editButton.className = "edit";
                editButton.textContent = "Edit";

                editButton.addEventListener("click", function() {
                    editStudent(student);
                });

                const deleteButton = document.createElement("button");
                deleteButton.type = "button";
                deleteButton.className = "delete";
                deleteButton.textContent = "Delete";

                deleteButton.addEventListener("click", function() {
                    deleteStudent(student.id);
                });

                actionButtons.appendChild(editButton);
                actionButtons.appendChild(deleteButton);
                actionCell.appendChild(actionButtons);
                row.appendChild(actionCell);

                tableBody.appendChild(row);
            });

        } catch (error) {
            tableBody.replaceChildren();

            const row = document.createElement("tr");
            const cell = document.createElement("td");

            cell.colSpan = 8;
            cell.textContent = error.message;

            row.appendChild(cell);
            tableBody.appendChild(row);

            showMessage(error.message, "error");

        } finally {
            loading.hidden = true;
        }
    }


    // Add or update student
    form.addEventListener("submit", async function(event) {
        event.preventDefault();
        clearMessage();

        if (!form.reportValidity()) {
            return;
        }

        const id = studentId.value;

        const student = {
            name: nameInput.value.trim(),
            email: emailInput.value.trim(),
            mobile: mobileInput.value.trim(),
            course_id: courseInput.value,
            year: yearInput.value,
            gender: genderInput.value
        };

        const isEditing = id !== "";

        if (isEditing) {
            student.id = Number(id);
        }

        submitButton.disabled = true;
        submitButton.textContent = "Saving...";

        try {
            const result = await apiRequest(API_URL, {
                method: isEditing ? "PUT" : "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify(student)
            });

            showMessage(result.message, "success");

            resetForm();
            await loadStudents();

        } catch (error) {
            showMessage(error.message, "error");

        } finally {
            submitButton.disabled = false;
            submitButton.textContent = studentId.value
                ? "Update Student"
                : "Add Student";
        }
    });


    // Fill form with selected student's details
    function editStudent(student) {
        clearMessage();

        studentId.value = student.id;
        nameInput.value = student.name;
        emailInput.value = student.email;
        mobileInput.value = student.mobile;
        courseInput.value = student.course_id ?? "";
        yearInput.value = student.year ?? "";
        genderInput.value = student.gender ?? "";

        formHeading.textContent = "Edit Student";
        submitButton.textContent = "Update Student";
        cancelButton.hidden = false;

        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    }


    // Delete student after confirmation
    async function deleteStudent(id) {
        const confirmed = confirm(
            "Are you sure you want to delete student ID " + id + "?"
        );

        if (!confirmed) {
            return;
        }

        clearMessage();

        try {
            const result = await apiRequest(API_URL, {
                method: "DELETE",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify({
                    id: Number(id)
                })
            });

            showMessage(result.message, "success");

            if (studentId.value === String(id)) {
                resetForm();
            }

            await loadStudents();

        } catch (error) {
            showMessage(error.message, "error");
        }
    }


    // Reset form to add mode
    function resetForm() {
        form.reset();
        studentId.value = "";

        formHeading.textContent = "Add Student";
        submitButton.textContent = "Add Student";
        cancelButton.hidden = true;
    }


    cancelButton.addEventListener("click", function() {
        resetForm();
        clearMessage();
    });


    form.addEventListener("reset", function() {
        studentId.value = "";

        formHeading.textContent = "Add Student";
        submitButton.textContent = "Add Student";
        cancelButton.hidden = true;
    });


    // Debounced search
    searchInput.addEventListener("input", function() {
        clearTimeout(searchTimer);

        searchTimer = setTimeout(function() {
            loadStudents();
        }, 300);
    });


    // Display existing records on page load
    loadStudents();
</script>

</body>
</html>
