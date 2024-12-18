// Sidebar
document.querySelectorAll(".sidebar-links a").forEach(link => {
    link.addEventListener("click", function (event) {
        if (this.getAttribute("href") === "/home" || this.getAttribute("id") === "logout") {
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

// Fonction de notification existante
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

// Vérification client du formulaire informations
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form[name='user_profile']");
    const requiredFields = ["name", "lastname", "firstname"];
    let toastDisplayed = false;

    form.addEventListener("submit", function (event) {
        if (confirm("Voulez vous vraiment modifier ces données")) {
            let formIsValid = true;

            const errorType = 'error';
            const errorIcon = 'fa-solid fa-circle-xmark';
            const errorTitle = translations.errorTitle;
            const errorText = translations.errorText;

            document.querySelectorAll(".input-label").forEach(label => label.style.color = "");
            document.querySelectorAll(".error-message").forEach(error => error.remove());
            toastDisplayed = false;

            requiredFields.forEach(function (fieldName) {
                const input = form.querySelector(`[name="user_profile[${fieldName}]"]`);
                const label = input ? input.closest('.input-box').querySelector('.input-label') : null;

                if (input && !input.value.trim()) {
                    formIsValid = false;

                    if (label) {
                        label.style.color = "red";
                    }

                    if (!toastDisplayed) {
                        createToast(errorType, errorIcon, errorTitle, errorText);
                        toastDisplayed = true;
                    }
                }
            });

            if (!formIsValid) {
                event.preventDefault();
            }
        }
        else {
            event.preventDefault();
        }
    });
});

// RECADRAGE DE L'IMAGE
let cropper;

document.getElementById('file-input').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        document.getElementById('image-name').value = file.name;
        const reader = new FileReader();
        reader.onload = function (event) {
            const cropContainer = document.querySelector('.crop-container');
            const imageToCrop = document.getElementById('image-to-crop');
            imageToCrop.src = event.target.result;
            cropContainer.style.display = 'flex';
            initializeCropper();
        };
        reader.readAsDataURL(file);
    }
});

function initializeCropper() {
    const image = document.getElementById('image-to-crop');
    cropper = new Cropper(image, {
        aspectRatio: 1,
        viewMode: 1,
    });
}

// Prévisualisation de l'image
document.getElementById('crop-button').addEventListener('click', function () {
    const canvas = cropper.getCroppedCanvas({
        width: 300,
        height: 300,
    });

    const croppedImageData = canvas.toDataURL('image/png');
    document.getElementById('current-profile-picture').src = croppedImageData;
    const imageName = document.getElementById('image-name').value;
    console.log(imageName);

    cropper.destroy();
    document.querySelector('.crop-container').style.display = 'none';
    document.querySelector('.save-button').style.display = 'block';
});

// Fonction pour fermer le cropper sans enregistrer
function closeCrop() {
    cropper.destroy();
    document.querySelector('.crop-container').style.display = 'none';
}

// Envoi de la photo de profil au serveur
document.querySelector('.save-button').addEventListener('click', function () {
    const croppedImage = document.getElementById('current-profile-picture').src;
    const imageName = document.getElementById('image-name').value;

    if (!croppedImage || !imageName) {
        return;
    }

    const formData = new FormData();
    formData.append('image', croppedImage);
    formData.append('imageName', imageName);

    fetch('/upload-profile-picture', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                localStorage.setItem('notification', 'Photo de profil changée avec succès');
                location.reload();
            }
            else {
                console.error(data.error);
            }
        })
        .catch((error) => {
            console.error('Erreur:', error);
        });
});

// Notification après changement de photo de profil
const notification = localStorage.getItem('notification');
if (notification) {
    const PPTitle = translations.PPTitle;
    const PPText = translations.PPText;
    createToast('success', 'fa-solid fa-check', PPTitle, PPText);
    localStorage.removeItem('notification');
}

// Vérification client formulaire d'adresse
document.addEventListener("DOMContentLoaded", function () {
    const addressForm = document.querySelector("#addressForm");
    const requiredFields = ["address_form[street]", "address_form[postalCode]", "address_form[city]", "address_form[country]"];
    let toastDisplayed = false;

    addressForm.addEventListener("submit", function (event) {
        console.log("Form submission event triggered");

        if (confirm("Voulez-vous vraiment modifier ces données ?")) {
            let formIsValid = true;

            const errorType = 'error';
            const errorIcon = 'fa-solid fa-circle-xmark';
            const errorTitle = translations.errorTitle;
            const errorText = translations.errorText;

            document.querySelectorAll(".input-label").forEach(label => label.style.color = "");
            document.querySelectorAll(".error-message").forEach(error => error.remove());
            toastDisplayed = false;

            requiredFields.forEach(function (fieldName) {
                const input = addressForm.querySelector(`[name="${fieldName}"]`);
                const label = input ? input.closest('.input-box').querySelector('.input-label') : null;

                if (input && !input.value.trim()) {
                    formIsValid = false;

                    if (label) {
                        label.style.color = "red";
                    }

                    if (!toastDisplayed) {
                        createToast(errorType, errorIcon, errorTitle, errorText);
                        toastDisplayed = true;
                    }
                }
            });

            if (!formIsValid) {
                event.preventDefault();
            }
        } else {
            event.preventDefault();
        }
    });
});

// Script vérification mot de passe
document.addEventListener("DOMContentLoaded", function () {
    const passwordForm = document.querySelector("#passwordForm");
    const requiredFields = [
        "password_form[oldPassword]",
        "password_form[newPassword]",
        "password_form[confirmPassword]"
    ];
    let toastDisplayed = false;

    passwordForm.addEventListener("submit", function (event) {
        console.log("Form submission event triggered");

        if (confirm("Voulez-vous vraiment modifier votre mot de passe ?")) {
            let formIsValid = true;

            const errorType = 'error';
            const errorIcon = 'fa-solid fa-circle-xmark';
            const errorTitle = 'Erreur';
            const errorText = 'Veuillez remplir tous les champs requis.';

            // Réinitialisation des erreurs
            document.querySelectorAll(".input-label").forEach(label => label.style.color = "");
            document.querySelectorAll(".error-message").forEach(error => error.remove());
            toastDisplayed = false;

            // Vérification des champs requisF
            requiredFields.forEach(function (fieldName) {
                const input = passwordForm.querySelector(`[name="${fieldName}"]`);
                const label = input ? input.closest('.input-box').querySelector('.input-label') : null;

                if (input && !input.value.trim()) {
                    formIsValid = false;

                    if (label) {
                        label.style.color = "red";
                    }

                    if (!toastDisplayed) {
                        createToast(errorType, errorIcon, errorTitle, errorText);
                        toastDisplayed = true;
                    }
                }
            });

            if (!formIsValid) {
                event.preventDefault();
            }
        } else {
            event.preventDefault();
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