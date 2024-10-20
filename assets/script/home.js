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

                    mangaGrid.innerHTML += `
                        <div class="manga-card ${borderClass}">
                        <img src="/images/Manga/${manga.image.url}" alt="${manga.name}">                           
                        <h2>${manga.name}</h2>
                            <p class="price">${manga.price}€</p>
                            <div class="ratings">${generateRatings(manga.rating)}</div>
                        </div>
                    `;
                });
            })
            .catch(error => {
                console.error('Erreur lors de la recherche:', error);
            });
    });

    function generateRatings(rating) {
        let stars = '';
        const starRating = Math.round(rating / 2); // Convertir en échelle de 5 étoiles

        for (let i = 1; i <= 5; i++) {
            if (i <= starRating) {
                stars += '<i class="fas fa-star"></i>'; // Étoile pleine
            } else if (i - starRating < 1) {
                stars += '<i class="fas fa-star-half-alt"></i>'; // Demi-étoile
            } else {
                stars += '<i class="far fa-star"></i>'; // Étoile vide
            }
        }

        return stars;
    }
});

// console.log(mangas); // Ajoutez ceci pour voir la structure des données
