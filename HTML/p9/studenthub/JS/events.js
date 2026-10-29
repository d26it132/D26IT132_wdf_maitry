
const eventsContainer = document.getElementById("eventsContainer");
const searchInput = document.getElementById("searchEvent");
const categoryFilter = document.getElementById("categoryFilter");
const eventCount = document.getElementById("eventCount");

let allEvents = [];

// Fetch event data from JSON
fetch("../JSON/events.json")
    .then(response => {
        if (!response.ok) {
            throw new Error("Unable to load event data");
        }
        return response.json();
    })
    .then(data => {
        allEvents = data;
        displayEvents(allEvents);
    })
    .catch(error => {
        eventsContainer.textContent =
            "Unable to load events. Please try again later.";
        eventCount.textContent = "";
        console.error(error);
    });

// Display event cards
function displayEvents(events) {
    eventsContainer.replaceChildren();

    eventCount.textContent =
        `${events.length} event${events.length !== 1 ? "s" : ""} found`;

    if (events.length === 0) {
        const message = document.createElement("p");
        message.className = "no-events";
        message.textContent = "No events found. Try another search.";
        eventsContainer.appendChild(message);
        return;
    }

    events.forEach(event => {
        const card = document.createElement("article");
        card.className = "event-card";

        const category = document.createElement("span");
        category.className = "event-category";
        category.textContent = event.category;

        const title = document.createElement("h3");
        title.textContent = event.title;

        const description = document.createElement("p");
        description.textContent = event.description;

        const date = document.createElement("p");
        const dateLabel = document.createElement("strong");
        dateLabel.textContent = "Date: ";
        date.append(dateLabel, document.createTextNode(event.date));

        const venue = document.createElement("p");
        const venueLabel = document.createElement("strong");
        venueLabel.textContent = "Venue: ";
        venue.append(venueLabel, document.createTextNode(event.venue));

        card.append(category, title, description, date, venue);
        eventsContainer.appendChild(card);
    });
}

// Search and category filtering
function filterEvents() {
    const searchText = searchInput.value.trim().toLowerCase();
    const selectedCategory = categoryFilter.value;

    const filteredEvents = allEvents.filter(event => {
        const matchesSearch =
            event.title.toLowerCase().includes(searchText) ||
            event.category.toLowerCase().includes(searchText);

        const matchesCategory =
            selectedCategory === "All" ||
            event.category === selectedCategory;

        return matchesSearch && matchesCategory;
    });

    displayEvents(filteredEvents);
}

searchInput.addEventListener("input", filterEvents);
categoryFilter.addEventListener("change", filterEvents);

// Mobile navigation menu
const menuButton = document.getElementById("menuButton");
const navigationMenu = document.getElementById("navigationMenu");

if (menuButton && navigationMenu) {
    menuButton.addEventListener("click", () => {
        const isOpen = navigationMenu.classList.toggle("active");

        menuButton.setAttribute("aria-expanded", isOpen);
        menuButton.setAttribute(
            "aria-label",
            isOpen ? "Close navigation menu" : "Open navigation menu"
        );
    });
}
