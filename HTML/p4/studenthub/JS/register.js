// register.js

let registerForm = document.getElementById("registerForm");

if (registerForm) {

    registerForm.addEventListener("submit", function (event) {

        event.preventDefault();

        let name = document.getElementById("name").value;
        let email = document.getElementById("email").value;
        let studentId = document.getElementById("studentid").value;
        let course = document.getElementById("course").value;
        let password = document.getElementById("password").value;

        let message = document.getElementById("registerMessage");

        if (
            name === "" ||
            email === "" ||
            studentId === "" ||
            password === ""
        ) {

            message.textContent =
                "Please fill in all required fields.";

            message.style.color = "red";

        } else {

            message.textContent =
                "Registration successful! Welcome " + name + ".";

            message.style.color = "green";

            console.log("Name:", name);
            console.log("Email:", email);
            console.log("Student ID:", studentId);
            console.log("Course:", course);

            registerForm.reset();

        }

    });

}
