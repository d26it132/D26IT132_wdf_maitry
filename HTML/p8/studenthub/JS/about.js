// about.js

let features = document.getElementById("features");

if (features) {

    let heading = features.querySelector("h3");

    heading.addEventListener("click", function () {

        let list = features.querySelector("ul");

        if (list.style.display === "none") {

            list.style.display = "block";

        } else {

            list.style.display = "none";

        }

    });

}
