// faq.js

let questions = document.querySelectorAll(".question");

questions.forEach(function (question) {

    question.addEventListener("click", function () {

        let answer = this.nextElementSibling;

        if (answer.style.display === "none") {

            answer.style.display = "block";

        } else {

            answer.style.display = "none";

        }

    });

});



const searchFAQ = document.getElementById("searchFAQ");
const categoryFilter = document.getElementById("categoryFilter");
const faqContainer = document.getElementById("faqContainer");
const faqCount = document.getElementById("faqCount");

let faqs = [];

// Fetch FAQ data from JSON
fetch("../JSON/faq.json")
    .then(response => {
        if (!response.ok) {
            throw new Error("Failed to load FAQ data");
        }

        return response.json();
    })
    .then(data => {
        faqs = data.faqs;
        displayFAQs(faqs);
    })
    .catch(error => {
        faqCount.textContent = "Unable to load FAQs.";
        console.error(error);
    });

// Display FAQs
function displayFAQs(list) {
    faqContainer.innerHTML = "";

    if (list.length === 0) {
        faqCount.textContent = "No FAQs found.";
        return;
    }

    faqCount.textContent = "Showing " + list.length + " FAQs";

    list.forEach(faq => {
        const article = document.createElement("article");
        const question = document.createElement("h3");
        const answer = document.createElement("p");
        const category = document.createElement("small");

        question.textContent = faq.question;
        answer.textContent = faq.answer;
        category.textContent = "Category: " + faq.category;

        article.appendChild(question);
        article.appendChild(answer);
        article.appendChild(category);

        faqContainer.appendChild(article);
    });
}

// Search and filter FAQs
function filterFAQs() {
    const keyword = searchFAQ.value.toLowerCase().trim();
    const selectedCategory = categoryFilter.value;

    const filteredFAQs = faqs.filter(faq => {
        const matchesSearch =
            faq.question.toLowerCase().includes(keyword) ||
            faq.answer.toLowerCase().includes(keyword);

        const matchesCategory =
            selectedCategory === "all" ||
            faq.category === selectedCategory;

        return matchesSearch && matchesCategory;
    });

    displayFAQs(filteredFAQs);
}

// Update results when user searches or changes category
searchFAQ.addEventListener("input", filterFAQs);
categoryFilter.addEventListener("change", filterFAQs);
