document.querySelectorAll(".dropdown").forEach(dropdown => {
    const button = dropdown.querySelector(".dropbtn");
    const menu = dropdown.querySelector(".dropdown-content");

    // Variable pour vérifier si le menu est actif
    let isMenuOpen = false;

    // Affiche ou cache le menu déroulant au clic
    button.addEventListener("click", (e) => {
        e.stopPropagation(); // Empêche la fermeture immédiate
        if (!isMenuOpen) {
            closeAllDropdowns(); // Ferme les autres menus ouverts
            menu.classList.add("active");
            isMenuOpen = true;
        } else {
            menu.classList.remove("active");
            isMenuOpen = false;
        }
    });

    // Ferme le menu lorsque la souris quitte le menu
    menu.addEventListener("mouseleave", () => {
        menu.classList.remove("active");
        isMenuOpen = false;
    });

    // Ferme le menu si on clique ailleurs sur la page
    document.addEventListener("click", () => {
        menu.classList.remove("active");
        isMenuOpen = false;
    });
});

// Fonction pour fermer tous les menus ouverts
function closeAllDropdowns() {
    document.querySelectorAll(".dropdown-content").forEach(menu => {
        menu.classList.remove("active");
    });
}