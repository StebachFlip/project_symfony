document.querySelectorAll(".sidebar-links a").forEach(link => {
    link.addEventListener("click", function (event) {
        // Vérifie si le lien pointe vers /home ou la route de déconnexion
        if (this.getAttribute("href") === "/home") {
            return; // Laisse le lien se comporter normalement, redirigeant vers /home ou déconnectant l'utilisateur
        }

        event.preventDefault(); // Empêche le lien de recharger la page pour les autres liens

        // Récupère la section cible depuis l'attribut href du lien
        const targetSection = this.getAttribute("href").substring(1); // Supprime le '/' pour obtenir seulement le nom

        // Masque toutes les sections en enlevant la classe 'active'
        document.querySelectorAll(".user-info").forEach(section => {
            section.classList.remove("active");
        });

        // Affiche la section cible en ajoutant la classe 'active'
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

document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form[name='user_profile']");
    const requiredFields = ["name", "lastname", "firstname"]; // Liste des champs requis
    let toastDisplayed = false; // Garde trace si un toast a été affiché

    form.addEventListener("submit", function (event) {
        if (confirm("Voulez vous vraiment modifier ces données")) {
            let formIsValid = true;

            // Paramètres pour la notification d'erreur
            const errorType = 'error';
            const errorIcon = 'fa-solid fa-circle-xmark';
            const errorTitle = translations.errorTitle;
            const errorText = translations.errorText;

            // Réinitialise l'affichage des labels et supprime les messages d'erreur précédents
            document.querySelectorAll(".input-label").forEach(label => label.style.color = "");
            document.querySelectorAll(".error-message").forEach(error => error.remove());
            toastDisplayed = false;

            // Boucle sur chaque champ requis pour vérifier s'il est vide
            requiredFields.forEach(function (fieldName) {
                const input = form.querySelector(`[name="user_profile[${fieldName}]"]`);
                const label = input ? input.closest('.input-box').querySelector('.input-label') : null;

                if (input && !input.value.trim()) {
                    formIsValid = false;

                    // Change la couleur du label associé au champ vide
                    if (label) {
                        label.style.color = "red";
                    }

                    // Affiche une notification d'erreur unique s'il y a un champ vide
                    if (!toastDisplayed) {
                        createToast(errorType, errorIcon, errorTitle, errorText);
                        toastDisplayed = true;
                    }
                }
            });

            // Empêche la soumission du formulaire si un champ requis est manquant
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

document.getElementById('crop-button').addEventListener('click', function () {
    const canvas = cropper.getCroppedCanvas({
        width: 300,
        height: 300,
    });

    // Prévisualisation de l'image rognée
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

document.querySelector('.save-button').addEventListener('click', function () {
    const croppedImage = document.getElementById('current-profile-picture').src;
    const imageName = document.getElementById('image-name').value;

    if (!croppedImage || !imageName) {
        return;
    }

    // Creation d'un formulaire pour envoyer les données
    const formData = new FormData();
    formData.append('image', croppedImage);
    formData.append('imageName', imageName);

    // Envoyer les données au serveur
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

const notification = localStorage.getItem('notification');
if (notification) {
    const PPTitle = translations.PPTitle;
    const PPText = translations.PPText;
    createToast('success', 'fa-solid fa-check', PPTitle, PPText);
    localStorage.removeItem('notification');
}