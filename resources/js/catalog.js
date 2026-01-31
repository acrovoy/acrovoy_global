document.addEventListener("DOMContentLoaded", () => {
    const catalogToggle = document.getElementById("catalogToggle");
    const catalogMenu = document.getElementById("catalogMenu");

    if (!catalogToggle || !catalogMenu) return;

    catalogToggle.addEventListener("click", (e) => {
        e.stopPropagation();
        catalogMenu.classList.toggle("hidden");
    });

    document.addEventListener("click", (e) => {
        if (!catalogMenu.contains(e.target) && !catalogToggle.contains(e.target)) {
            catalogMenu.classList.add("hidden");
        }
    });
});
