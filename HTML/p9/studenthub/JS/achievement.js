// achievement.js

let achievements = document.querySelectorAll("main section article");

achievements.forEach(function (achievement) {

    let button = document.createElement("button");

    button.textContent = "Highlight";

    achievement.appendChild(button);

    button.addEventListener("click", function () {

        achievement.style.backgroundColor = "#e8f5e9";

        button.textContent = "Highlighted";

        button.disabled = true;

    });

});
