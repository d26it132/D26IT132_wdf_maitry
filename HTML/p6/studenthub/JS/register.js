// Get the registration form
const form = document.getElementById("registerForm");

// Run when Register button is clicked
form.addEventListener("submit", function(event) {

    // Stop the form from submitting
    event.preventDefault();

    // Get values from input fields
    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const studentid = document.getElementById("studentid").value.trim();
    const number = document.getElementById("number").value.trim();
    const course = document.getElementById("course").value;
    const password = document.getElementById("password").value;
    const recheckPassword =
        document.getElementById("recheckPassword").value;


    // 1. Check Name
    if (name === "") {
        alert("Please enter your full name.");
        document.getElementById("name").focus();
        return;
    }


    // 2. Check Email
    if (email === "") {
        alert("Please enter your email address.");
        document.getElementById("email").focus();
        return;
    }


    // 3. Check Student ID
    if (studentid === "") {
        alert("Please enter your Student ID.");
        document.getElementById("studentid").focus();
        return;
    }


    // 4. Check Mobile Number
    if (number === "") {
        alert("Please enter your mobile number.");
        document.getElementById("number").focus();
        return;
    }

    // Check whether number contains exactly 10 digits
    if (!/^[678][0-9]{9}$/.test(number)) {
        alert("Mobile number must be 10 digits and start with 6, 7, or 8.");
        document.getElementById("number").focus();
        return;
    }


    // 5. Check Course
    if (course === "") {
        alert("Please select your course.");
        document.getElementById("course").focus();
        return;
    }


    // 6. Check Password
    if (password === "") {
        alert("Please enter a password.");
        document.getElementById("password").focus();
        return;
    }

    // Password must have at least 6 characters
    if (password.length < 6) {
        alert("Password must contain at least 6 characters.");
        document.getElementById("password").focus();
        return;
    }


    // 7. Check Recheck Password
    if (recheckPassword === "") {
        alert("Please re-enter your password.");
        document.getElementById("recheckPassword").focus();
        return;
    }


    // 8. Compare passwords
    if (password !== recheckPassword) {
        alert("Passwords do not match.");
        document.getElementById("recheckPassword").focus();
        return;
    }


    // 9. Ask for confirmation
    const confirmRegistration = confirm(
        "Do you want to submit the registration?"
    );

    if (!confirmRegistration) {
        return;
    }


    // 10. Registration successful
    alert("Registration successful!");

    // Clear all fields
    form.reset();

});
