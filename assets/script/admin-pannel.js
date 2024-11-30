// Sidebar
document.querySelectorAll(".sidebar-links a").forEach(link => {
    link.addEventListener("click", function (event) {
        if (this.getAttribute("href") === "/home" || this.getAttribute("id") === "logout") {
            return;
        }

        event.preventDefault();

        const targetSection = this.getAttribute("href").substring(1);

        document.querySelectorAll(".user-info").forEach(section => {
            section.classList.remove("active");
        });

        const activeSection = document.querySelector(`.user-info[data-section="${targetSection}"]`);
        if (activeSection) {
            activeSection.classList.add("active");
        }
    });
});