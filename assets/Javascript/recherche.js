document.addEventListener('DOMContentLoaded', () => {
    // 🔍 Gestion du pop-up de recherche
    const searchButton = document.querySelector('.search-button'); // Bouton pour ouvrir
    const searchPopup = document.querySelector('.search-popup'); // Pop-up
    const overlay = document.querySelector('.overlay'); // Overlay qui sert à fermer

    // Fonction pour ouvrir le pop-up
    const openPopup = () => {
        searchPopup.classList.add('active');
        overlay.classList.add('active');
        document.body.classList.add('no-scroll'); // Empêche le défilement en arrière-plan
    };

    // Fonction pour fermer le pop-up
    const closePopup = () => {
        searchPopup.classList.remove('active');
        overlay.classList.remove('active');
        document.body.classList.remove('no-scroll'); // Restaure le défilement
    };

    // Ouvrir le pop-up au clic sur le bouton
    searchButton.addEventListener('click', (e) => {
        e.stopPropagation(); // Empêche le clic de se propager
        openPopup();
    });

    // Fermer le pop-up lorsqu'on clique sur l'overlay ou ailleurs sur la page
    document.addEventListener('click', (e) => {
        if (!searchPopup.contains(e.target) && !searchButton.contains(e.target)) {
            closePopup();
        }
    });

    // Optionnel : Fermer le pop-up avec la touche "Échap"
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && searchPopup.classList.contains('active')) {
            closePopup();
        }
    });

    // Empêcher les clics dans le pop-up de fermer celui-ci
    searchPopup.addEventListener('click', (e) => {
        e.stopPropagation(); // Empêche le clic dans le pop-up de se propager
    });

    // 🔍 Gestion de la recherche dynamique
    const searchInput = document.querySelector('.form-control'); // Champ de recherche
    const resultsContainer = document.querySelector('.search-suggestions ul'); // Conteneur des résultats

    let debounceTimeout;

    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.trim();

        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            if (query.length > 1) { // Ne recherche que si au moins 2 caractères sont saisis
                fetch(`/search?q=${encodeURIComponent(query)}`)
                    .then((response) => response.json())
                    .then((data) => {
                        displayResults(data.results);
                    })
                    .catch((error) => console.error('Erreur lors de la recherche :', error));
            } else {
                resultsContainer.innerHTML = ''; // Efface les résultats si le champ est vide
            }
        }, 300); // Délai pour éviter trop de requêtes
    });

    function displayResults(results) {
        resultsContainer.innerHTML = ''; // Réinitialise les résultats

        if (results.length === 0) {
            resultsContainer.innerHTML = '<li>Aucun résultat trouvé</li>';
            return;
        }

        results.forEach((result) => {
            const li = document.createElement('li');
            li.classList.add('search-result-item');

            const link = document.createElement('a');

            // Modifie le lien et le contenu selon le type de résultat
            if (result.type === 'article') {
                link.href = `/articles/${result.id}`;
                link.innerHTML = `
                    <img src="${result.image || '/images/default-placeholder.png'}" alt="${result.title}" class="result-icon">
                    <div>
                        <strong>${result.title}</strong>
                        <p>${result.content}</p>
                    </div>
                `;
            } else if (result.type === 'player') {
                link.href = `/players/${result.id}`;
                link.innerHTML = `
                    <img src="${result.image || '/images/default-placeholder.png'}" alt="${result.name}" class="result-icon">
                    <div>
                        <strong>${result.name}</strong>
                        <p>${result.team} - ${result.age} ans - ${result.country}</p>
                    </div>
                `;
            } else if (result.type === 'team') {
                link.href = `/teams/${result.id}`;
                link.innerHTML = `
                    <img src="${result.image || '/images/default-placeholder.png'}" alt="${result.name}" class="result-icon">
                    <div>
                        <strong>${result.name}</strong>
                        <p>${result.country || ''}</p>
                    </div>
                `;
            }

            li.appendChild(link);
            resultsContainer.appendChild(li);
        });
    }
});