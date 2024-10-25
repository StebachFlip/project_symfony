let success = document.querySelector(".input-submit");

success.onclick = function () {
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