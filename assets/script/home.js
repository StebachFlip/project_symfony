// script/home.js

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search");
    const mangaGrid = document.querySelector(".manga-grid");

    searchInput.addEventListener("input", function () {
        const searchTerm = this.value;

        fetch(`/home/search?name=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(mangas => {
                mangaGrid.innerHTML = ''; // Vider la grille des mangas

                if (mangas.length === 0) {
                    // Afficher le message centré
                    mangaGrid.innerHTML = '<p class="no-results">Aucun manga trouvé.</p>';
                    return;
                }

                mangas.forEach(manga => {
                    const borderClasses = ['red', 'orange', 'yellow', 'green', 'blue'];
                    const borderClass = borderClasses[mangas.indexOf(manga) % borderClasses.length];

                    var chaine = ""
                    if (manga.stock > 0) {
                        chaine = `<p class="price">${manga.price}€</p>`;
                    }
                    else {
                        chaine = '<p class="price">Indisponible</p>';
                    }

                    mangaGrid.innerHTML += `
                        <div class="manga-card ${borderClass}">
                        <a href="/manga/${manga.link}">
                        <img src="/images/Manga/${manga.image.url}" alt="${manga.name}">                           
                        <h2>${manga.name}</h2>
                            `+ chaine + `
                        </a>
                        </div>
                    `;
                });
            })
            .catch(error => {
                console.error('Erreur lors de la recherche:', error);
            });
    });
});

// console.log(mangas); // Ajoutez ceci pour voir la structure des données
