// Sidebar
document.querySelectorAll(".sidebar-links a").forEach(link => {
    link.addEventListener("click", function (event) {
        if (this.getAttribute("href") === "/home" || this.getAttribute("href") === "/admin-pannel") {
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

//REDIRECTION AVEC MESSAGE DE CONFIRMATIONS
const link = document.getElementById('return');

// Ajouter un événement de clic sur le lien
link.addEventListener('click', function (event) {
    // Empêcher le comportement par défaut du lien (changement de page)
    event.preventDefault();

    // Afficher une boîte de confirmation
    const userConfirmed = confirm("Voulez-vous vraiment retourner à la page précédente ?");

    // Si l'utilisateur confirme, rediriger vers l'URL du lien
    if (userConfirmed) {
        window.location.href = link.href;
    }
});

// Empêche le rafraîchissement de la page lorsque l'utilisateur clique sur le bouton "Parcourir"
function handleUploadButtonClick(event) {
    event.preventDefault(); // Empêche l'action par défaut (rafraîchissement de la page)
    document.getElementById('file-input').click(); // Ouvre la boîte de dialogue du fichier
}

// ROGNAGE DE L'IMAGE
let cropper;
let imageToCrop = document.getElementById('image-to-crop');
let croppedImageContainer = document.getElementById('cropped-image-container');
let croppedImage = document.getElementById('cropped-image');
let fileInput = document.getElementById('file-input');

// Lorsque l'utilisateur choisit un fichier
fileInput.addEventListener('change', function (event) {
    event.preventDefault();
    let file = event.target.files[0];
    if (file) {
        // Réinitialiser le conteneur d'image rognée à chaque nouveau fichier sélectionné
        croppedImageContainer.style.display = 'none'; // Cacher l'image rognée précédente
        croppedImage.src = ''; // Réinitialiser l'image rognée
        // Créer un URL pour l'image sélectionnée
        let reader = new FileReader();
        reader.onload = function (e) {
            // Afficher l'image à rogner dans le crop-container
            imageToCrop.src = e.target.result;
            // Afficher le crop-container pour le rognage
            document.querySelector('.crop-container').style.display = 'block';

            // Initialiser le Cropper.js
            if (cropper) {
                cropper.destroy(); // Détruire l'instance précédente si nécessaire
            }
            cropper = new Cropper(imageToCrop, {
                aspectRatio: 14.8 / 21, // Ratio largeur:hauteur de l'image rognée
                viewMode: 1,
                autoCropArea: 0.65,
                responsive: true
            });
        };
        reader.readAsDataURL(file);
    }
});

// Lorsque l'utilisateur confirme le rognage
document.getElementById('crop-button').addEventListener('click', function (event) {
    event.preventDefault(); // Empêche le rafraîchissement ou toute autre action par défaut

    // Récupérer l'image rognée
    let croppedCanvas = cropper.getCroppedCanvas();

    // Convertir l'image rognée en base64 et l'afficher
    let croppedImageUrl = croppedCanvas.toDataURL();
    croppedImage.src = croppedImageUrl;

    // Afficher l'image rognée sous le bouton
    croppedImageContainer.style.display = 'block';

    // Cacher le crop-container après le rognage
    document.querySelector('.crop-container').style.display = 'none';
});

// Lorsque l'utilisateur annule le rognage
document.getElementById('cancel-button').addEventListener('click', function (event) {
    event.preventDefault(); // Empêche l'action par défaut
    // Cacher le crop-container sans rien faire
    document.querySelector('.crop-container').style.display = 'none';
});


document.addEventListener('DOMContentLoaded', function () {
    const validateButton = document.getElementById('validate-button');
    const form = document.getElementById('form');
    const croppedImageContainer = document.getElementById('cropped-image-container');
    let cropper;

    validateButton.addEventListener('click', function (event) {
        event.preventDefault();
        let errors = [];

        const name = document.getElementById('manga_form_name').value.trim();
        const author = document.getElementById('manga_form_author').value.trim();
        const price = document.getElementById('price').value.trim();
        const stock = document.getElementById('stock').value.trim();
        const description = document.getElementById('manga_form_description').value.trim();

        console.log("name:" + name, "author:" + author, "price:" + price, "stock:" + stock, "desc:" + description);

        if (!name) errors.push('Le nom du manga est requis.');
        if (!author) errors.push('Le nom de l\'auteur est requis.');
        if (!price || isNaN(price) || parseFloat(price) <= 0) errors.push('Le prix du manga doit être un nombre positif.');
        if (!stock || isNaN(stock) || parseInt(stock) <= 0) errors.push('La quantité en stock doit être un nombre positif.');
        if (!description) errors.push('La description est requise.');

        // Vérifier si l'image rognée existe
        const croppedImage = document.getElementById('cropped-image').src;
        if (croppedImage === '') {
            errors.push('Une image est requise.');
        }

        if (errors.length > 0) {
            alert(errors.join('\n')); // Afficher les erreurs
            return;
        }

        // Ajouter l'image rognée au formulaire sous forme de champ caché
        const imageInput = document.createElement('input');
        imageInput.type = 'hidden';
        imageInput.name = 'croppedImage'; // Nom du champ à envoyer
        imageInput.value = croppedImage; // Ajouter l'image rognée (en base64)
        form.appendChild(imageInput); // Ajouter au formulaire

        // Préparer les données du formulaire pour l'envoi via AJAX
        const formData = new FormData(form);

        const mangaId = document.getElementById('manga_form_id').value.trim();
        if (mangaId) {
            formData.append('id', mangaId); // Ajouter l'ID du manga pour modification
        }

        // Envoi AJAX pour enregistrer le manga
        fetch('/admin/manga/addOrUpdate', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    const mangaId = data.mangaId;

                    // Après l'enregistrement du manga, envoyer l'image
                    const croppedImageData = croppedImage.split(',')[1]; // Extraire la partie Base64 de l'URL
                    sendImageToServer(croppedImageData, mangaId);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
                alert('Une erreur est survenue.');
            });
    });

    function sendImageToServer(croppedImageData, mangaId) {
        const imageData = new FormData();
        imageData.append('image', croppedImageData); // Envoi de l'image base64
        imageData.append('mangaId', mangaId); // ID du manga pour associer l'image

        fetch('/admin/manga/uploadImage', {
            method: 'POST',
            body: imageData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log('Image sauvegardée avec succès');
                    console.log('Chemin de l\'image:', data.imagePath);
                } else {
                    console.error('Erreur lors de l\'upload de l\'image', data.message);
                }
            })
            .catch(error => {
                console.error('Erreur lors de l\'envoi de l\'image:', error);
            });
    }
});

function confirmDelete() {
    // Affiche une boîte de dialogue de confirmation
    return confirm('Êtes-vous sûr de vouloir supprimer ce manga ?');
}