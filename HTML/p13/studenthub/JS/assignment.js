// assignment.js

let rows = document.querySelectorAll("table tbody tr");

rows.forEach(function (row) {

    let statusCell = row.cells[3];

    let buttonCell = document.createElement("td");

    let button = document.createElement("button");

    button.textContent = "Mark Completed";

    buttonCell.appendChild(button);

    row.appendChild(buttonCell);

    button.addEventListener("click", function () {

        statusCell.textContent = "Completed";

        button.textContent = "Completed";

        button.disabled = true;

    });

});
