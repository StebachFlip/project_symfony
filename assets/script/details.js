document.addEventListener("DOMContentLoaded", function () {
    const buyButton = document.querySelector('.buy-button');
    const quantitySelect = document.querySelector('.quantity-select');
    const cartCountElement = document.getElementById('cart-count'); // Récupérer l'élément du compteur

    // Fonction pour mettre à jour le compteur du panier
    function updateCartCount() {
        fetch('/cart/count', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    cartCountElement.textContent = data.cart_count; // Mettre à jour le compteur avec la somme des quantités
                } else {
                    console.error('Erreur lors de la récupération du nombre d\'éléments dans le panier');
                }
            })
            .catch(error => {
                console.error('Erreur AJAX:', error);
            });
    }

    function createToast(type, icon, title, text) {
        let notifications = document.querySelector('.notification');
        let newToast = document.createElement('div');
        newToast.innerHTML = `
                <div class="toast ${type}">
                    <i class="${icon}"></i>
                    <div class="content">
                        <div class="title">${title}</div>
                        <span>${text}</span>
                    </div>
                    <i id="cross" class="fa-solid fa-xmark" onclick="this.parentElement.remove()"></i>
                </div>`;
        notifications.appendChild(newToast);
        newToast.timeOut = setTimeout(() => newToast.remove(), 5000);
    }

    // Mettre à jour le nombre d'articles du panier lors du chargement de la page
    updateCartCount();

    if (buyButton) {
        buyButton.addEventListener('click', function () {
            const mangaId = buyButton.getAttribute('data-manga-id'); // Récupérer l'ID du manga
            const quantity = quantitySelect.value; // La quantité choisie par l'utilisateur

            // Vérifier que l'ID du manga et la quantité sont valides
            if (!mangaId || !quantity) {
                alert('Une erreur est survenue.');
                return;
            }

            // Utilisation de fetch pour envoyer la requête à Symfony
            fetch(`/cart/add/${mangaId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new URLSearchParams({
                    'quantity': quantity
                })
            })
                .then(response => response.json()) // Parseg JSON comme avant
                .then(data => {
                    if (data.status === 'success') {
                        const errorType = 'success';
                        const errorIcon = 'fa-solid fa-circle-xmark';
                        const errorTitle = 'Ajout dans le panier';
                        const errorText = "Le manga a été ajouté au panier avec succès";
                        createToast(errorType, errorIcon, errorTitle, errorText);

                        // Mettre à jour le compteur du panier avec le nombre total d'articles
                        updateCartCount();
                    } else {
                        // Si la réponse contient un message d'erreur
                        const errorType = 'error';
                        const errorIcon = 'fa-solid fa-circle-exclamation';
                        const errorTitle = 'Erreur lors de l\'ajout';
                        const errorText = data.message; // Message d'erreur retourné par le serveur
                        createToast(errorType, errorIcon, errorTitle, errorText);
                    }
                })
                .catch(error => {
                    console.error("Erreur lors de l'ajout au panier:", error);
                    const errorType = 'error';
                    const errorIcon = 'fa-solid fa-circle-exclamation';
                    const errorTitle = 'Erreur lors de l\'ajout';
                    const errorText = "Impossible de dépasser le stock maximum de ce manga"; // Message d'erreur générique
                    createToast(errorType, errorIcon, errorTitle, errorText);
                });
        });
    }
});
