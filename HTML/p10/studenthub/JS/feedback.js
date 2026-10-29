// feedback.js

let feedbackForm = document.getElementById("feedbackForm");

if (feedbackForm) {

    feedbackForm.addEventListener("submit", function (event) {

        event.preventDefault();

        let name = document.getElementById("name").value;
        let rating = document.getElementById("rating").value;
        let feedback = document.getElementById("feedback").value;

        if (name === "" || feedback === "") {

            alert("Please enter your name and feedback.");

        } else {

            alert(
                "Thank you " + name +
                "! Your feedback has been submitted."
            );

            console.log("Rating:", rating);
            console.log("Feedback:", feedback);

            feedbackForm.reset();

        }

    });

}
