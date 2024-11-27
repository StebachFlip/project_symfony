document.addEventListener("DOMContentLoaded", function () {
    // Récupérer toutes les options de paiement existantes (cartes existantes)
    const paymentOptions = Array.from(document.querySelectorAll('.payment-option')).map((option, index) => {
        const checkbox = document.getElementById(`card-payment-${index + 1}`);
        const form = document.getElementById(`card-payment-form-${index + 1}`);
        const optionDiv = option;

        // Vérification si l'élément existe
        if (!checkbox || !form) {
            console.warn(`Formulaire ou checkbox manquant pour l'index ${index + 1}`);
            return null; // Retourner null si l'élément est manquant
        }

        return { checkbox, form, option: optionDiv };
    }).filter(Boolean); // Filtrer les éléments nuls

    // Formulaire d'ajout de carte
    const addCardCheckbox = document.getElementById('card-payment-add');
    const addCardFormContainer = document.getElementById('card-payment-form-add');
    const addCardOption = document.getElementById('card-payment-option-add'); // Option "Ajouter une nouvelle carte"

    // Option PayPal
    const paypalCheckbox = document.getElementById('paypal-payment');
    const paypalFormContainer = document.getElementById('paypal-payment-form');
    const paypalOption = document.getElementById('paypal-payment-option'); // Div de l'option PayPal

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
            if (form) form.style.display = 'none'; // Cacher tous les formulaires existants
            if (option) option.style.display = 'block'; // Réafficher les checkboxes des cartes existantes
        });
        if (addCardFormContainer) addCardFormContainer.style.display = 'none'; // Cacher le formulaire d'ajout de carte
        if (addCardOption) addCardOption.style.display = 'block'; // Réafficher la checkbox "Ajouter une nouvelle carte"
        if (paypalFormContainer) paypalFormContainer.style.display = 'none'; // Cacher le formulaire PayPal
        if (paypalOption) paypalOption.style.display = 'block'; // Réafficher l'option PayPal
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
                        checkbox.checked = false; // Désélectionner toutes les autres
                    }
                });
                paypalOption.style.display = 'block';
                paypalCheckbox.checked = false;

                // Masquer tous les formulaires des cartes existantes
                paymentOptions.forEach(({ form }) => {
                    if (form) form.style.display = 'none'; // Cacher les formulaires des cartes existantes
                });
                paypalFormContainer.style.display = 'none';

                // Afficher le formulaire d'ajout de carte
                if (addCardFormContainer) {
                    addCardFormContainer.style.display = 'block';
                }

                // Cacher la checkbox "Ajouter une nouvelle carte"
                if (addCardOption) {
                    addCardOption.style.display = 'none'; // Cacher la checkbox "Ajouter une nouvelle carte"
                    paymentOptions.forEach(({ option }) => {
                        option.style.display = "block";
                    })
                }
            } else {
                // Si la checkbox "Ajouter une nouvelle carte" est décochée
                if (addCardFormContainer) {
                    addCardFormContainer.style.display = 'none'; // Cacher le formulaire d'ajout de carte
                }

                // Afficher la checkbox "Ajouter une nouvelle carte"
                if (addCardOption) {
                    addCardOption.style.display = 'block'; // Afficher la checkbox
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
                        checkbox.checked = false; // Désélectionner toutes les autres
                    }
                });
                resetForms(); // Réinitialiser tous les autres formulaires
                if (paypalFormContainer) paypalFormContainer.style.display = 'block'; // Afficher le formulaire PayPal
                if (paypalOption) paypalOption.style.display = 'none'; // Cacher la checkbox PayPal
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
                    if (option) option.style.display = 'none'; // Cacher la checkbox sélectionnée

                    addCardCheckbox.checked = false;
                    paypalCheckbox.checked = false;

                    // Désélectionner toutes les autres checkboxes
                    paymentOptions.forEach(({ checkbox: otherCheckbox }, otherIndex) => {
                        if (otherIndex !== index && otherCheckbox) {
                            otherCheckbox.checked = false; // Désélectionner les autres
                        }
                    });

                    // Afficher le formulaire correspondant
                    showForm(index);
                } else {
                    // Si la checkbox est décochée, afficher à nouveau la checkbox
                    if (option) option.style.display = 'block'; // Réafficher la checkbox si elle est décochée

                    // Cacher le formulaire correspondant
                    resetForms();
                }
            });
        }
    });

    // Initialiser l'affichage au chargement
    resetForms();
});

// LOGIQUE DE SUPPRESION D'UNE CARTE
document.addEventListener("DOMContentLoaded", function () {
    const deleteButtons = document.querySelectorAll(".delete-card-btn");

    deleteButtons.forEach(button => {
        button.addEventListener("click", function () {
            const cardId = button.getAttribute("data-card-id");
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Récupérer le token CSRF

            if (confirm("Voulez-vous vraiment supprimer cette carte ?")) {
                fetch(`/delete-card/${cardId}?_token=${csrfToken}`, {
                    method: "DELETE",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    }
                })
                    .then(response => {
                        if (response.ok) {
                            alert("Carte supprimée avec succès.");
                            window.location.replace("/profile");
                        } else {
                            alert("Une erreur est survenue lors de la suppression de la carte.");
                        }
                    })
                    .catch(error => {
                        console.error("Erreur :", error);
                        alert("Une erreur est survenue lors de la suppression de la carte.");
                    });
            }
        });
    });
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
                    <span>Entrez une adresse mail et un mot de passe.</span>
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

