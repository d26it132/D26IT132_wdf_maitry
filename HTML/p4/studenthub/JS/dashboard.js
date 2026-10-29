// dashboard.js

let cards = document.querySelectorAll(".card");

cards.forEach(function (card) {

    card.addEventListener("click", function () {

        card.style.transform = "scale(1.05)";

        setTimeout(function () {

            card.style.transform = "scale(1)";

        }, 300);

    });

});