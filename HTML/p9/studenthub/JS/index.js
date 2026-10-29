// index.js

let welcomeMessage = document.querySelector("main h2");

if (welcomeMessage) {

    welcomeMessage.addEventListener("click", function () {

        alert("Welcome to StudentHub!");

    });

}

document.getElementById("closeNotification").addEventListener("click", function() {
    document.getElementById("notificationBanner").style.display = "none";
});

let slides = document.querySelectorAll(".slide");

let previousButton = document.getElementById("previousSlide");
let nextButton = document.getElementById("nextSlide");
let slideNumber = document.getElementById("slideNumber");

let currentSlide = 0;


// Show selected slide
function showSlide(index) {

    // Remove active from all slides
    for (let i = 0; i < slides.length; i++) {
        slides[i].classList.remove("active");
    }

    // Add active to selected slide
    slides[index].classList.add("active");

    // Update slide number
    slideNumber.textContent = (index + 1) + " / " + slides.length;
}


// Next button
nextButton.addEventListener("click", function () {

    currentSlide++;

    if (currentSlide >= slides.length) {
        currentSlide = 0;
    }

    showSlide(currentSlide);
});


// Previous button
previousButton.addEventListener("click", function () {

    currentSlide--;

    if (currentSlide < 0) {
        currentSlide = slides.length - 1;
    }

    showSlide(currentSlide);
});


const themeToggle = document.getElementById("themeToggle");

// Load the saved theme when the page opens
const savedTheme = localStorage.getItem("studentHubTheme");

if (savedTheme === "dark") {
    document.body.classList.add("dark-mode");
}

function updateThemeButton() {
    const isDark = document.body.classList.contains("dark-mode");

    themeToggle.textContent = isDark ? "☀️ Light Mode" : "🌙 Dark Mode";

    themeToggle.setAttribute(
        "aria-label",
        isDark ? "Switch to light mode" : "Switch to dark mode"
    );
}

// Set the correct button text on page load
updateThemeButton();

// Switch between light and dark mode
themeToggle.addEventListener("click", function () {
    document.body.classList.toggle("dark-mode");

    const isDark = document.body.classList.contains("dark-mode");

    localStorage.setItem(
        "studentHubTheme",
        isDark ? "dark" : "light"
    );

    updateThemeButton();
});
