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

document.addEventListener('DOMContentLoaded', function () {
    const minusButtons = document.querySelectorAll('.quantity-btn.minus');
    const plusButtons = document.querySelectorAll('.quantity-btn.plus');
    const removeButtons = document.querySelectorAll('.remove-btn');

    const emptyMessage = document.querySelector('.empty-basket-message');
    const cartItemsContainer = document.getElementById('cart-items-container');

    // Met à jour le message panier vide
    function toggleEmptyMessage() {
        if (cartItemsContainer.children.length === 0) {
            emptyMessage.style.display = 'block';
        } else {
            emptyMessage.style.display = 'none';
        }
    }

    // Met à jour le total du panier
    function updateTotal() {
        let total = 0;

        document.querySelectorAll('.item-price').forEach(priceElement => {
            const price = parseFloat(priceElement.textContent.replace(' €', '').replace(',', '.'));
            console.log("Prix détecté pour l'article :", priceElement, "->", price);

            if (!isNaN(price)) {
                total += price;
            } else {
                console.error("Erreur : prix non valide détecté", priceElement.textContent);
            }
        });

        document.getElementById('total-price').textContent = total.toFixed(2).replace('.', ',') + ' €';
    }



    // Gère la modification des quantités (augmentation ou diminution)
    async function handleQuantityChange(event) {
        const button = event.target;
        const itemId = button.getAttribute('data-item-id');
        const quantityElement = document.querySelector(`.item-quantity[data-item-id="${itemId}"]`);
        const priceElement = document.querySelector(`.item-price[data-item-id="${itemId}"]`);
        const stock = parseInt(button.getAttribute('data-stock'));
        const unitPrice = parseFloat(priceElement.getAttribute('data-price'));
        if (isNaN(unitPrice)) {
            console.error(`Prix unitaire invalide pour l'article ${itemId}:`, priceElement.getAttribute('data-price'));
            return;
        }
        let currentQuantity = parseInt(quantityElement.textContent);

        let url, method;
        if (button.getAttribute('data-action') === 'increase') {
            if (currentQuantity < stock) {
                url = `/cart/increase/${itemId}`;
                method = 'POST';
            } else {
                const errorType = 'error';
                const errorIcon = 'fa-solid fa-circle-xmark';
                const errorTitle = 'Erreur';
                const errorText = `La quantité maximale disponible est ${stock}.`;
                createToast(errorType, errorIcon, errorTitle, errorText);
                return;
            }
        } else if (button.getAttribute('data-action') === 'decrease') {
            if (currentQuantity > 1) {
                url = `/cart/decrease/${itemId}`;
                method = 'POST';
            } else {
                const errorType = 'error';
                const errorIcon = 'fa-solid fa-circle-xmark';
                const errorTitle = 'Erreur';
                const errorText = "La quantité ne peut pas être inférieure à 1";
                createToast(errorType, errorIcon, errorTitle, errorText);

                return;
            }
        }

        try {
            const response = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' }
            });

            const result = await response.json();

            if (result.success) {
                // Met à jour la quantité et le prix
                quantityElement.textContent = result.newQuantity;
                priceElement.textContent = (result.newQuantity * unitPrice).toFixed(2).replace('.', ',') + ' €';

                // Met à jour le total du panier
                updateTotal();
            } else {
                console.error("Erreur lors de la mise à jour de la quantité.");
            }
        } catch (error) {
            console.error("Erreur réseau :", error);
        }
    }

    // Gère la suppression d'un article
    async function handleRemoveItem(event) {
        const button = event.target.closest('.remove-btn');
        const itemId = button.getAttribute('data-item-id');
        const basketItem = document.querySelector(`.basket-item[data-item-id="${itemId}"]`);

        try {
            const response = await fetch(`/cart/remove/${itemId}`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' }
            });

            const result = await response.json();

            if (result.success) {
                // Supprime l'article du DOM
                basketItem.remove();

                // Met à jour le total et le message panier vide
                const errorType = 'success';
                const errorIcon = 'fa-solid fa-circle-xmark';
                const errorTitle = 'Suppression';
                const errorText = "Le manga à été supprimé avec succès";
                createToast(errorType, errorIcon, errorTitle, errorText);
                updateTotal();
                toggleEmptyMessage();
            } else {
                console.error("Erreur lors de la suppression de l'article.");
            }
        } catch (error) {
            console.error("Erreur réseau :", error);
        }
    }

    // Ajouter des écouteurs d'événements
    minusButtons.forEach(button => {
        button.addEventListener('click', handleQuantityChange);
    });

    plusButtons.forEach(button => {
        button.addEventListener('click', handleQuantityChange);
    });

    removeButtons.forEach(button => {
        button.addEventListener('click', handleRemoveItem);
    });

    // Initialiser le message panier vide au chargement
    toggleEmptyMessage();
});

document.addEventListener('DOMContentLoaded', function () {
    const cartItems = document.querySelectorAll('.basket-item');
    const emptyMessage = document.querySelector('.empty-basket-message');

    // Vérifiez si le panier est vide
    if (cartItems.length === 0) {
        emptyMessage.style.display = 'block';
    } else {
        emptyMessage.style.display = 'none';
    }
});
