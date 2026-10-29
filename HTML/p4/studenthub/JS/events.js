// events.js

let events = document.querySelectorAll("main section article");

events.forEach(function (eventItem) {

    let button = document.createElement("button");

    button.textContent = "Register for Event";

    eventItem.appendChild(button);

    button.addEventListener("click", function () {

        button.textContent = "Registered";

        button.disabled = true;

        alert("You have successfully registered for this event.");

    });

});
