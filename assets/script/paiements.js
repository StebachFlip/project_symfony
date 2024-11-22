document.addEventListener("DOMContentLoaded", function () {
    // Sélectionner les cases à cocher
    const cardPaymentCheckbox = document.getElementById('card-payment');
    const paypalPaymentCheckbox = document.getElementById('paypal-payment');

    // Les containers de formulaires
    const cardPaymentForm = document.getElementById('card-payment-form');
    const paypalPaymentForm = document.getElementById('paypal-payment-form');

    // Les options de paiement
    const cardPaymentOption = document.getElementById('card-payment-option');
    const paypalPaymentOption = document.getElementById('paypal-payment-option');

    // Fonction pour gérer l'affichage des formulaires
    function updateForms() {
        if (cardPaymentCheckbox.checked) {
            // Si Carte de Crédit est cochée
            cardPaymentForm.style.display = 'block';  // Afficher le formulaire carte
            paypalPaymentForm.style.display = 'none'; // Cacher le formulaire PayPal
            paypalPaymentOption.style.display = 'flex'; // Réafficher l'option PayPal
            cardPaymentOption.style.display = 'none';  // Cacher l'option Carte de Crédit
        } else if (paypalPaymentCheckbox.checked) {
            // Si PayPal est cochée
            paypalPaymentForm.style.display = 'block'; // Afficher le formulaire PayPal
            cardPaymentForm.style.display = 'none';    // Cacher le formulaire Carte
            cardPaymentOption.style.display = 'flex';  // Réafficher l'option Carte de Crédit
            paypalPaymentOption.style.display = 'none'; // Cacher l'option PayPal
        } else {
            // Si aucune option n'est sélectionnée, réinitialiser l'affichage
            cardPaymentForm.style.display = 'none';
            paypalPaymentForm.style.display = 'none';
            cardPaymentOption.style.display = 'flex';
            paypalPaymentOption.style.display = 'flex';
        }
    }

    // Ajouter un écouteur d'événements sur les cases à cocher
    cardPaymentCheckbox.addEventListener('change', function () {
        // Assurez-vous qu'une seule option est sélectionnée à la fois
        if (cardPaymentCheckbox.checked) {
            paypalPaymentCheckbox.checked = false; // Décocher PayPal si Carte est cochée
        }
        updateForms();
    });

    paypalPaymentCheckbox.addEventListener('change', function () {
        // Assurez-vous qu'une seule option est sélectionnée à la fois
        if (cardPaymentCheckbox.checked = false) {
            cardPaymentCheckbox.checked = false; // Décocher Carte si PayPal est cochée
        }
        updateForms();
    });

    // Initialiser l'affichage des formulaires au chargement
    updateForms();
    cardPaymentCheckbox.checked = false;
    paypalPaymentCheckbox.checked = false;
});
