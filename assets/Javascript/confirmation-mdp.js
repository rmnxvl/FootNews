document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.contact-form');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const errorMessage = document.getElementById('password-error');

    form.addEventListener('submit', (event) => {
        if (passwordInput.value !== confirmPasswordInput.value) {
            event.preventDefault(); // Empêche l'envoi du formulaire
            errorMessage.style.display = 'block';
        } else {
            errorMessage.style.display = 'none';
        }
    });
});