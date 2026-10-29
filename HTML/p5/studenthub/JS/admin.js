// admin.js

let adminArticles = document.querySelectorAll("main section article");

adminArticles.forEach(function (article) {

    article.addEventListener("click", function () {

        article.style.backgroundColor = "#e8f5e9";

        alert("Admin section selected.");

    });

});
