// Sidebar
document.querySelectorAll(".sidebar-links a").forEach(link => {
    link.addEventListener("click", function (event) {
        if (this.getAttribute("href") === "/home" || this.getAttribute("href") === "/manga-form") {
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

// Afficher le detail des commandes
document.addEventListener('DOMContentLoaded', function () {
    const orderDetailsButtons = document.querySelectorAll('.quantity-btn.plus');

    orderDetailsButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Récupère l'ID de la commande depuis le bouton
            const orderId = button.getAttribute('data-order-id');

            // Trouve le conteneur des articles de cette commande spécifique
            const orderItemsContainer = document.querySelector(`#items-${orderId}`);

            if (!orderItemsContainer) {
                console.error('No order items container found for order ID:', orderId);
                return;
            }

            // Ferme tous les autres détails de commande
            document.querySelectorAll('.order-items').forEach(container => {
                if (container !== orderItemsContainer && container.style.display === 'block') {
                    container.style.display = 'none';
                }
            });

            // Bascule l'affichage du conteneur des articles de la commande
            orderItemsContainer.style.display =
                orderItemsContainer.style.display === 'block' ? 'none' : 'block';
        });
    });
});

document.querySelectorAll('.quantity-btn.plus-bis').forEach(button => {
    button.addEventListener('click', function () {
        var month = this.getAttribute('data-month');
        var orderItemsContainer = document.getElementById('items-' + month);

        // Fermer tous les autres conteneurs
        document.querySelectorAll('.order-items').forEach(container => {
            if (container !== orderItemsContainer) {
                container.style.display = 'none';
            }
        });

        // Afficher ou masquer le conteneur de ce mois
        if (orderItemsContainer.style.display === 'none') {
            orderItemsContainer.style.display = 'block';
        } else {
            orderItemsContainer.style.display = 'none';
        }
    });
});


