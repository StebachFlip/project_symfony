document.addEventListener("DOMContentLoaded", function () {
    const buyButton = document.querySelector('.buy-button');
    const quantitySelect = document.querySelector('.quantity-select');
    const cartCountElement = document.getElementById('cart-count');

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
                    cartCountElement.textContent = data.cart_count;
                } else {
                    console.error('Erreur lors de la récupération du nombre d\'éléments dans le panier');
                }
            })
            .catch(error => {
                console.error('Erreur AJAX:', error);
            });
    }

    // Mettre à jour le nombre d'articles du panier lors du chargement de la page
    updateCartCount();
});