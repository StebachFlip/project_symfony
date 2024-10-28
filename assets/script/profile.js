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

const dropZone = document.getElementById('drop-zone');

dropZone.addEventListener('dragover', (event) => {
    event.preventDefault();
    dropZone.style.backgroundColor = 'rgba(33, 38, 77, 0.2)';
    dropZone.style.color = '#FFF';
});

dropZone.addEventListener('dragleave', () => {
    dropZone.style.backgroundColor = '';
    dropZone.style.color = '#AAA';
});

dropZone.addEventListener('drop', (event) => {
    event.preventDefault();
    dropZone.style.backgroundColor = '';
    dropZone.style.color = '#AAA';

    const files = event.dataTransfer.files;
    // Vous pouvez ensuite manipuler les fichiers ici
    console.log(files); // Pour vérifier les fichiers dans la console
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