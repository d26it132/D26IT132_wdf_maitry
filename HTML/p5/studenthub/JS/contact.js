// contact.js

let contactForm = document.getElementById("contactForm");

if (contactForm) {

    contactForm.addEventListener("submit", function (event) {

        event.preventDefault();

        let name = document.getElementById("name").value;
        let email = document.getElementById("email").value;
        let message = document.getElementById("message").value;

        if (name === "" || email === "" || message === "") {

            alert("Please fill in all fields.");

        } else {

            alert(
                "Thank you " + name +
                "! Your message has been sent."
            );

            contactForm.reset();

        }

    });

}
