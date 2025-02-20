// Récupérer les éléments du DOM
const toggle = document.getElementById("darkmode-toggle");
const logo = document.getElementById("logo");

// Vérifier l'état du mode sombre dans le localStorage
const isDarkMode = localStorage.getItem("dark-mode") === "true";

// Définir les chemins des logos (assurez-vous qu'ils sont corrects dans public/Images/)
const logoLightPath = "/Images/logo-light.png"; // Chemin absolu depuis public/
const logoDarkPath = "/Images/logo-dark.png";   // Chemin absolu depuis public/

// Appliquer l'état initial
if (isDarkMode) {
    document.body.classList.add("dark-mode"); // Activer le mode sombre
    toggle.checked = true; // Mettre à jour le toggle
    if (logo) logo.src = logoDarkPath; // Utiliser le logo sombre
} else {
    document.body.classList.remove("dark-mode"); // Désactiver le mode sombre
    toggle.checked = false; // Mettre à jour le toggle
    if (logo) logo.src = logoLightPath; // Utiliser le logo clair
}

// Ajouter un écouteur d'événement pour le toggle
toggle.addEventListener("change", () => {
    if (toggle.checked) {
        // Mode sombre activé
        document.body.classList.add("dark-mode");
        localStorage.setItem("dark-mode", "true"); // Enregistrer la préférence
        if (logo) logo.src = logoDarkPath; // Changer vers le logo sombre
    } else {
        // Mode clair activé
        document.body.classList.remove("dark-mode");
        localStorage.setItem("dark-mode", "false"); // Enregistrer la préférence
        if (logo) logo.src = logoLightPath; // Retourner au logo clair
    }
});