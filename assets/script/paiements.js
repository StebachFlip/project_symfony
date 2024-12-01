document.addEventListener("DOMContentLoaded", function () {
    // Récupérer toutes les options de paiement existantes (cartes existantes)
    const paymentOptions = Array.from(document.querySelectorAll('.payment-option')).map((option, index) => {
        const checkbox = document.getElementById(`card-payment-${index + 1}`);
        const form = document.getElementById(`card-payment-form-${index + 1}`);
        const optionDiv = option;

        // Vérification si l'élément existe
        if (!checkbox || !form) {
            console.warn(`Formulaire ou checkbox manquant pour l'index ${index + 1}`);
            return null;
        }

        return { checkbox, form, option: optionDiv };
    }).filter(Boolean);

    // Formulaire d'ajout de carte
    const addCardCheckbox = document.getElementById('card-payment-add');
    const addCardFormContainer = document.getElementById('card-payment-form-add');
    const addCardOption = document.getElementById('card-payment-option-add');

    // Option PayPal
    const paypalCheckbox = document.getElementById('paypal-payment');
    const paypalFormContainer = document.getElementById('paypal-payment-form');
    const paypalOption = document.getElementById('paypal-payment-option');

    // Vérifier si le formulaire d'ajout de carte existe
    if (!addCardCheckbox || !addCardFormContainer || !addCardOption) {
        console.warn("Le formulaire d'ajout de carte ou la checkbox d'ajout sont manquants.");
    }

    // Vérifier si PayPal est bien configuré
    if (!paypalCheckbox || !paypalFormContainer || !paypalOption) {
        console.warn("Le formulaire PayPal ou la checkbox sont manquants.");
    }

    // Fonction pour réinitialiser tous les formulaires et réafficher toutes les options
    function resetForms() {
        paymentOptions.forEach(({ form, option }) => {
            if (form) form.style.display = 'none';
            if (option) option.style.display = 'block';
        });
        if (addCardFormContainer) addCardFormContainer.style.display = 'none';
        if (addCardOption) addCardOption.style.display = 'block';
        if (paypalFormContainer) paypalFormContainer.style.display = 'none';
        if (paypalOption) paypalOption.style.display = 'block';
    }

    // Fonction pour afficher un formulaire spécifique et cacher sa checkbox
    function showForm(selectedIndex) {
        resetForms();
        paymentOptions.forEach(({ form, option }, index) => {
            if (index === selectedIndex) {
                if (form) form.style.display = 'block'; // Afficher le formulaire
                if (option) {
                    option.style.display = 'none'; // Cacher la checkbox
                    addCardCheckbox.checked = false;
                    paypalCheckbox.check = false;
                }
            }
        });
    }

    // Afficher le formulaire d'ajout de carte lorsque l'option correspondante est cochée
    if (addCardCheckbox) {
        addCardCheckbox.addEventListener('change', function () {
            if (addCardCheckbox.checked) {
                // Désélectionner toutes les autres checkboxes
                paymentOptions.forEach(({ checkbox }) => {
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                });
                paypalOption.style.display = 'block';
                paypalCheckbox.checked = false;

                // Masquer tous les formulaires des cartes existantes
                paymentOptions.forEach(({ form }) => {
                    if (form) form.style.display = 'none';
                });
                paypalFormContainer.style.display = 'none';

                // Afficher le formulaire d'ajout de carte
                if (addCardFormContainer) {
                    addCardFormContainer.style.display = 'block';
                }

                // Cacher la checkbox "Ajouter une nouvelle carte"
                if (addCardOption) {
                    addCardOption.style.display = 'none';
                    paymentOptions.forEach(({ option }) => {
                        option.style.display = "block";
                    })
                }
            } else {
                // Si la checkbox "Ajouter une nouvelle carte" est décochée
                if (addCardFormContainer) {
                    addCardFormContainer.style.display = 'none';
                }

                // Afficher la checkbox "Ajouter une nouvelle carte"
                if (addCardOption) {
                    addCardOption.style.display = 'block';
                }
            }
        });
    }

    // Gestion de l'option PayPal
    if (paypalCheckbox) {
        paypalCheckbox.addEventListener('change', function () {
            if (paypalCheckbox.checked) {
                addCardCheckbox.checked = false;
                paymentOptions.forEach(({ checkbox }) => {
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                });
                resetForms(); // Réinitialiser tous les autres formulaires
                if (paypalFormContainer) paypalFormContainer.style.display = 'block';
                if (paypalOption) paypalOption.style.display = 'none';
            } else {
                resetForms();
            }
        });
    }

    // Ajouter des écouteurs d'événements sur les cases à cocher des cartes existantes
    paymentOptions.forEach(({ checkbox, option }, index) => {
        if (checkbox) {
            checkbox.addEventListener('change', function () {
                if (checkbox.checked) {
                    // Masquer la checkbox de la carte sélectionnée
                    if (option) option.style.display = 'none';

                    addCardCheckbox.checked = false;
                    paypalCheckbox.checked = false;

                    // Désélectionner toutes les autres checkboxes
                    paymentOptions.forEach(({ checkbox: otherCheckbox }, otherIndex) => {
                        if (otherIndex !== index && otherCheckbox) {
                            otherCheckbox.checked = false;
                        }
                    });

                    // Afficher le formulaire correspondant
                    showForm(index);
                } else {
                    // Si la checkbox est décochée, afficher à nouveau la checkbox
                    if (option) option.style.display = 'block';

                    // Cacher le formulaire correspondant
                    resetForms();
                }
            });
        }
    });

    // Initialiser l'affichage au chargement
    resetForms();
});

// Message d'erreur si le formulaire n'est pas complet
let success = document.querySelector("#confirm");
success.onclick = function () {
    let notifications = document.querySelector('.notification');
    const numberInput = document.querySelector('#card_form_number');
    const dateInput = document.querySelector('#card_form_expirationDate');
    const cvvInput = document.querySelector('#card_form_cvv');

    if (numberInput.value == "" || dateInput.value == "" || cvvInput.value == "") {
        let newToast = document.createElement('div');
        newToast.innerHTML = `
            <div class="toast error">
                <i class="fa-solid fa-circle-xmark"></i>
                <div class="content">
                    <div class="title">Erreur</div>
                    <span>Veuillez remplir tout les champs.</span>
                </div>
                <i id ="cross" class="fa-solid fa-xmark" onclick="this.parentElement.remove()"></i>
            </div>`;
        notifications.appendChild(newToast);
        newToast.timeOut = setTimeout(() => newToast.remove(), 5000);
    }
    else {
        setTimeout(() => {

        })
    }
}

