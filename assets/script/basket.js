// Sidebar
document.querySelectorAll(".sidebar-links a").forEach(link => {
    link.addEventListener("click", function (event) {
        if (this.getAttribute("href") === "/home") {
            return;
        }

        event.preventDefault();

        const targetSection = this.getAttribute("href").substring(1);

        document.querySelectorAll(".user-info").forEach(section => {
            section.classList.remove("active");
        });

        const activeSection = document.querySelector(`.user-info[data-section="${targetSection}"]`);
        if (activeSection) {
            activeSection.classList.add("active");
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const minusButtons = document.querySelectorAll('.quantity-btn.minus');
    const plusButtons = document.querySelectorAll('.quantity-btn.plus');
    const removeButtons = document.querySelectorAll('.remove-btn');

    // Fonction pour mettre à jour le total du panier
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.item-price').forEach(priceElement => {
            total += parseFloat(priceElement.textContent.replace(' €', '').replace(',', '.'));
        });
        document.getElementById('total-price').textContent = total.toFixed(2) + ' €';
    }

    // Fonction pour gérer la mise à jour des quantités
    function handleQuantityChange(event) {
        const button = event.target;
        const itemId = button.getAttribute('data-item-id');
        const quantityElement = document.querySelector(`.item-quantity[data-item-id="${itemId}"]`);
        const stock = parseInt(button.getAttribute('data-stock'));  // Récupère le stock disponible
        let currentQuantity = parseInt(quantityElement.textContent);

        if (button.getAttribute('data-action') === 'increase') {
            if (currentQuantity < stock) {  // Vérifie si la quantité ne dépasse pas le stock
                quantityElement.textContent = currentQuantity + 1;
            } else {
                alert(`La quantité maximale disponible est ${stock}.`);
            }
        } else if (button.getAttribute('data-action') === 'decrease') {
            quantityElement.textContent = Math.max(1, currentQuantity - 1);  // On ne descend pas sous 1
        }

        // Met à jour le prix de l'article
        const priceElement = document.querySelector(`.item-price[data-item-id="${itemId}"]`);
        const price = parseFloat(priceElement.textContent.replace(' €', '').replace(',', '.')) / currentQuantity;  // Récupère le prix unitaire
        priceElement.textContent = (parseInt(quantityElement.textContent) * price).toFixed(2) + ' €';

        // Met à jour le total du panier
        updateTotal();
    }

    // Fonction pour gérer la suppression de l'article
    function handleRemoveItem(event) {
        // Empêche le comportement par défaut du bouton
        event.preventDefault();

        // Récupère l'ID de l'élément à supprimer à partir de l'attribut 'data-item-id' du bouton
        const button = event.target.closest('.remove-btn'); // S'assure de récupérer le bon bouton
        const itemId = button.getAttribute('data-item-id'); // Récupère l'ID

        console.log("Item ID to remove: ", itemId);  // Ajoute un log pour vérifier l'ID

        // Cherche l'élément du panier avec cet ID
        const basketItem = document.querySelector(`.basket-item[data-item-id="${itemId}"]`);

        console.log("Basket item found: ", basketItem);  // Vérifie si l'élément est trouvé

        // Vérifie si l'élément existe avant de tenter de le supprimer
        if (basketItem) {
            basketItem.remove();
            updateTotal();  // Met à jour le total après suppression
        } else {
            console.error("Article non trouvé !");
        }
    }
    // Ajouter des écouteurs d'événements aux boutons
    minusButtons.forEach(button => {
        button.addEventListener('click', handleQuantityChange);
    });

    plusButtons.forEach(button => {
        button.addEventListener('click', handleQuantityChange);
    });

    removeButtons.forEach(button => {
        button.addEventListener('click', handleRemoveItem);
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const cartItems = document.querySelectorAll('.basket-item');
    const emptyMessage = document.querySelector('.empty-basket-message');

    // Si aucun élément de panier n'est trouvé, afficher le message
    if (cartItems.length === 0) {
        emptyMessage.style.display = 'block';  // Affiche le message
    } else {
        emptyMessage.style.display = 'none';   // Masque le message si des articles sont présents
    }
});
