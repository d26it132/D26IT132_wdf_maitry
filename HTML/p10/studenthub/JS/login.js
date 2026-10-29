// login.js

let loginForm = document.getElementById("loginForm");

if (loginForm) {

    loginForm.addEventListener("submit", function (event) {

        event.preventDefault();

        let email = document.getElementById("email").value;
        let password = document.getElementById("password").value;

        if (email === "" || password === "") {

            alert("Please enter email and password.");

        } else {

            alert("Login successful!");

        }

    });

}
