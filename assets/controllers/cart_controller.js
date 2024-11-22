// assets/controllers/cart_controller.js
import { Controller } from "stimulus";

export default class extends Controller {
    static targets = ["quantity"];

    // Méthode pour augmenter la quantité
    increase() {
        const quantityInput = this.quantityTarget;
        const newQuantity = parseInt(quantityInput.value) + 1;
        quantityInput.value = newQuantity;
        this.updateCart(newQuantity);
    }

    // Méthode pour diminuer la quantité
    decrease() {
        const quantityInput = this.quantityTarget;
        const newQuantity = Math.max(1, parseInt(quantityInput.value) - 1); // On ne descend pas sous 1
        quantityInput.value = newQuantity;
        this.updateCart(newQuantity);
    }

    // Méthode pour envoyer la mise à jour au serveur (ou à un autre composant)
    updateCart(newQuantity) {
        // Vous pouvez envoyer une requête AJAX ici pour mettre à jour le panier côté serveur
        // Exemple : avec fetch ou axios, ou tout autre mécanisme de votre choix
        console.log(`Quantité mise à jour : ${newQuantity}`);
    }
}
