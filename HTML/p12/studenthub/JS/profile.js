// profile.js

let editButton = document.getElementById("editProfileBtn");

if (editButton) {

    editButton.addEventListener("click", function () {

        let newName = prompt("Enter your new name:");

        if (newName !== null && newName.trim() !== "") {

            document.getElementById("studentName").textContent = newName;

            alert("Profile updated successfully.");

        } else {

            alert("Name was not changed.");

        }

    });

}
