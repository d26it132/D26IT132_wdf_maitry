/* StudentHub Registration JavaScript */

// Get form and input elements
const registrationForm = document.getElementById("registrationForm");

const studentName = document.getElementById("studentName");
const email = document.getElementById("email");
const mobile = document.getElementById("mobile");
const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirmPassword");
const course = document.getElementById("course");
const year = document.getElementById("year");
const terms = document.getElementById("terms");


// Get error message elements
const nameError = document.getElementById("nameError");
const emailError = document.getElementById("emailError");
const mobileError = document.getElementById("mobileError");
const passwordStrength = document.getElementById("passwordStrength");
const passwordError = document.getElementById("passwordError");
const confirmPasswordError = document.getElementById("confirmPasswordError");
const courseError = document.getElementById("courseError");
const yearError = document.getElementById("yearError");
const genderError = document.getElementById("genderError");
const termsError = document.getElementById("termsError");


// Display error message
function showError(element, message) {
    element.textContent = message;
}

// Clear all error messages
function clearErrors() {
    nameError.textContent = "";
    emailError.textContent = "";
    mobileError.textContent = "";
    passwordStrength.textContent = "";
    passwordError.textContent = "";
    confirmPasswordError.textContent = "";
    courseError.textContent = "";
    yearError.textContent = "";
    genderError.textContent = "";
    termsError.textContent = "";
}


// Password strength checker
password.addEventListener("input", function () {

    const value = password.value;

    passwordStrength.textContent = "";

    if (value.length === 0) {
        return;
    }

    if (value.length < 8) {
        passwordStrength.textContent = "Weak password";
        passwordStrength.style.color = "red";
    }
    else if (
        /[A-Z]/.test(value) &&
        /[a-z]/.test(value) &&
        /[0-9]/.test(value) &&
        /[^A-Za-z0-9]/.test(value)
    ) {
        passwordStrength.textContent = "Strong password";
        passwordStrength.style.color = "green";
    }
    else {
        passwordStrength.textContent = "Medium password";
        passwordStrength.style.color = "orange";
    }
});


// Confirm password checker
confirmPassword.addEventListener("input", function () {

    if (confirmPassword.value === "") {
        confirmPasswordError.textContent = "";
    }
    else if (password.value !== confirmPassword.value) {
        confirmPasswordError.textContent = "Passwords do not match";
    }
    else {
        confirmPasswordError.textContent = "Passwords match";
        confirmPasswordError.style.color = "green";
    }
});


// Form submission validation
registrationForm.addEventListener("submit", function (event) {

    clearErrors();

    let isValid = true;

    // Name validation
    if (studentName.value.trim() === "") {
        showError(nameError, "Please enter your full name.");
        isValid = false;
    }
    else if (!/^[A-Za-z]+(?:[ .'-][A-Za-z]+)*$/.test(studentName.value.trim())) {
        showError(nameError, "Please enter a valid name.");
        isValid = false;
    }

    // Email validation
    if (email.value.trim() === "") {
        showError(emailError, "Please enter your email address.");
        isValid = false;
    }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        showError(emailError, "Please enter a valid email address.");
        isValid = false;
    }

    // Mobile number validation
    if (mobile.value.trim() === "") {
        showError(mobileError, "Please enter your mobile number.");
        isValid = false;
    }
    else if (!/^[6-9][0-9]{9}$/.test(mobile.value.trim())) {
        showError(mobileError, "Enter a valid 10-digit number starting with 6, 7, 8, or 9.");
        isValid = false;
    }

    // Password validation
    if (password.value === "") {
        showError(passwordError, "Please create a password.");
        isValid = false;
    }
    else if (password.value.length < 8) {
        showError(passwordError, "Password must contain at least 8 characters.");
        isValid = false;
    }
    else if (
        !/[A-Z]/.test(password.value) ||
        !/[a-z]/.test(password.value) ||
        !/[0-9]/.test(password.value) ||
        !/[^A-Za-z0-9]/.test(password.value)
    ) {
        showError(
            passwordError,
            "Use uppercase, lowercase, a number, and a special character."
        );
        isValid = false;
    }

    // Confirm password validation
    if (confirmPassword.value === "") {
        showError(confirmPasswordError, "Please confirm your password.");
        isValid = false;
    }
    else if (password.value !== confirmPassword.value) {
        showError(confirmPasswordError, "Passwords do not match.");
        isValid = false;
    }

    // Course validation
    if (course.value === "") {
        showError(courseError, "Please select your course.");
        isValid = false;
    }

    // Year validation
    if (year.value === "") {
        showError(yearError, "Please select your year.");
        isValid = false;
    }

    // Gender validation
    const selectedGender = document.querySelector(
        'input[name="gender"]:checked'
    );

    if (!selectedGender) {
        showError(genderError, "Please select your gender.");
        isValid = false;
    }

    // Terms and conditions validation
    if (!terms.checked) {
        showError(termsError, "Please accept the Terms and Conditions.");
        isValid = false;
    }

    // Stop submission if any validation fails
    if (!isValid) {
        event.preventDefault();
        return;
    }

    // Confirmation before submission
    const confirmation = confirm(
        "Do you want to submit the registration?"
    );

    if (!confirmation) {
        event.preventDefault();
    }

});


// Reset form and clear errors
registrationForm.addEventListener("reset", function () {

    clearErrors();

    setTimeout(function () {
        passwordStrength.textContent = "";
        confirmPasswordError.textContent = "";
    }, 0);

});


// Responsive navigation menu
const menuButton = document.getElementById("menuButton");
const navigationMenu = document.getElementById("navigationMenu");

if (menuButton && navigationMenu) {

    menuButton.addEventListener("click", function () {

        const isOpen = navigationMenu.classList.toggle("active");

        menuButton.setAttribute("aria-expanded", isOpen);

        menuButton.setAttribute(
            "aria-label",
            isOpen ? "Close navigation menu" : "Open navigation menu"
        );

        menuButton.textContent = isOpen ? "✕" : "☰";

    });

}