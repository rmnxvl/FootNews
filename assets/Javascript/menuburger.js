// Sélection des éléments
const burgerMenu = document.querySelector('.burger-menu');
const navMenu = document.querySelector('.nav-menu');

// Toggle du menu burger
burgerMenu.addEventListener('click', () => {
    navMenu.classList.toggle('active');
});