let success1 = document.querySelector("#submit1");

success1.onclick = function () {
    let notifications = document.querySelector('.notification');
    const emailInput = document.querySelector('input[name="email"]');
    const mdpInput = document.querySelector('input[name="password"]');

    if (emailInput.value == "" || mdpInput.value == "") {
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
}

let success2 = document.querySelector("#submit2");

success2.onclick = function () {
    let notifications = document.querySelector('.notification');
    const mailInput = document.querySelector('input[name="registration_form[email]"]');
    const nameInput = document.querySelector('input[name="registration_form[name]"]');
    const passInput = document.querySelector('input[name="registration_form[password]"]');
    const confirmInput = document.querySelector('input[name="registration_form[confirm_password]"]');

    if (mailInput.value == "" || nameInput.value == "" || passInput.value == "" || confirmInput.value == "") {
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
}




