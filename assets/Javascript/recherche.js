function toggleSearchPopup() {
  const popup = document.getElementById("search-popup");
  const body = document.body;

  // Basculer l'état du pop-up
  if (popup.style.display === "flex") {
    popup.style.display = "none";
    body.classList.remove("no-scroll"); // Permet le défilement
  } else {
    popup.style.display = "flex";
    body.classList.add("no-scroll"); // Désactive le défilement
  }
}

// Fermer le pop-up si on clique en dehors
window.addEventListener("click", function (e) {
  const popup = document.getElementById("search-popup");
  const searchButton = document.querySelector(".search-button");
  if (
    popup.style.display === "flex" &&
    !popup.contains(e.target) &&
    !searchButton.contains(e.target)
  ) {
    popup.style.display = "none";
    document.body.classList.remove("no-scroll"); // Permet le défilement
  }
});
